<?php

namespace App\Service;


use App\DTO\IngredientsRecipeDto;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Enum\StatusEnum;
use App\Factory\IngredientsRecipeDtoFactory;
use App\Factory\PrompDtoFactory;
use App\Factory\RecipeLogFactory;
use App\Service\Utils\ArrayUtils;
use App\Service\Utils\StringUtils;
use Doctrine\ORM\EntityManagerInterface;

class IngredientManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OpenAiHttpClient       $openAiHttp,
        private readonly RecipeDetailsManager   $recipeDetailsManager,
        private readonly RecipeLogManager       $recipeLogManager,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function setIngredientsToRecipe(Recipe $recipe, array $ingredients): void
    {
        $this->generateIngredientsForSpecificRecipe($recipe, $ingredients);

    }

    private function clearEntityManager(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * @throws \Exception
     */
    public function generateIngredientsForSpecificRecipe(Recipe $recipe, array $ingredients): void
    {
        //TODO select most important ingredients in recipe and then select 1000 ingredients random
        /**$order = 'Dans ta réponse, je veux que tu listes uniquement les ingrédients, je te demande par exemple une tarte au citron.
         * Ta réponse va être Citron : 10g; Sucre : 100g; Farine : 200g; Beurre : 100g; Oeuf : 2. Aussi si tu as un ingrédient comme des pommes de terre, des pommes, ne donne pas un nombre mais toujours un le nombre de gramme si possible.
         * Enfin si tu as des ingredients comme pomme, fait attention de bien récupérer les ingredients en base comme Pomme, pulpe et peau, crue, c est très important que j ai le nom exact. Et affiche les ingrédients que une seule fois. Si tu affiches plusieurs fois le meme type
         * d ingrédient, vérifie que c est bie nécessaire.
         * En te basant sur cette recette ' . $recipe->getName() . ' fournis moi les ingrédients nécessaire que
         * je t ai fournis. Je veux que tu utilises absolument à la lettre près les ingrédients que je t ai fournis.';**/

        $order = "Dans ta réponse, je veux que tu énumères uniquement les ingrédients. Par exemple, si je demande une tarte au citron, ta réponse devrait ressembler à ceci : Citron : 10g; Sucre : 100g; Farine : 200g; Beurre : 100g; Œuf : 2. Si tu rencontres des ingrédients tels que des pommes de terre ou des pommes, ne donne pas un nombre, mais plutôt le poids en grammes si possible. Assure-toi également de récupérer les ingrédients exacts de la base de données, par exemple, Pomme, pulpe et peau, crue. Il est crucial d'avoir le nom exact. De plus, assure-toi de n'afficher chaque type d'ingrédient qu'une seule fois, et vérifie si leur répétition est nécessaire. En te basant sur la recette '" . $recipe->getName() . "', fournis-moi les ingrédients requis. Assure-toi d'utiliser précisément les ingrédients que j'ai spécifiés.";

        $prompt = PrompDtoFactory::create(
            prompt: $order,
            message: "Basé uniquement sur les ingrédients suivants que je vais te fournir séparés par un point-virgule, je veux que tu utilises strictement et uniquement ces ingrédients. Aucun autre ingrédient ne doit être pris en compte. Je veux une liste précise avec les quantités en grammes pour cette recette : " . $recipe->getName() . ". Assure-toi de ne pas déroger à la liste que je te fournirai, même s'il y en a beaucoup. Voici la liste des ingrédients : " . $ingredients[0],
        );

        $response = $this->openAiHttp->request($prompt);

        $this->handleDataIngredientsPrompted($recipe, $response, $response);
        $this->entityManager->flush();
    }

    private function handleDataIngredientsPrompted(Recipe $recipe, string $ingredients, string $response): void
    {
        $ingredientsDto = $this->formatDataIngredientsPromptedInDto($ingredients);
        $missedIngredients = [];

        foreach ($ingredientsDto as $ingredientDto) {
            if (!$this->checkIfIngredientExist($ingredientDto->ingredients)) {
                $missedIngredients[] = $ingredientDto->ingredients;
            }

            $this->recipeDetailsManager->createRecipeDetails($recipe, $ingredientDto);
        }

        if (count($missedIngredients) > 0) {
            $this->recipeLogManager->createRecipeLog($recipe, $response, $missedIngredients);
        }

    }

    private function checkIfIngredientExist(string $ingredientName): bool
    {
        return $this->entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $ingredientName]) !== null;
    }

    /**
     * @return IngredientsRecipeDto[]
     */
    private function formatDataIngredientsPromptedInDto(string $ingredients): array
    {
        /** @var IngredientsRecipeDto[] $ingredients */
        $ingredientsDto = [];

        foreach (explode(';', $ingredients) as $ingredient) {
            $detailsIngredient = $this->getDataIngredientInFormatedArray($ingredient);

            $ingredientsDto[] = IngredientsRecipeDtoFactory::create(
                ingredientName: StringUtils::removeFirstSpaceAndlastOfstring($detailsIngredient[0]),
                quantity: StringUtils::removeFirstSpaceAndlastOfstring($detailsIngredient[1]),
            );
        }

        return $ingredientsDto;
    }

    public function getRandomIngredientsForCreatingRecipe(): array
    {
        $basicIngredient = $this->getBasicIngredientsForRecipe();
        $randomIngredients = $this->entityManager->getRepository(Ingredient::class)->findSomesRandomIngredients(700);
        return array_map(fn(Ingredient $ingredient) => $ingredient->getName(), $basicIngredient + $randomIngredients);
    }

    private function getSpecificIngredient(string $name): array
    {
        return $this->entityManager->getRepository(Ingredient::class)->findIngredientLikeName($name);
    }

    private function getBasicIngredientsForRecipe(): array
    {
        $beurres = $this->getSpecificIngredient('beurre à');
        $pepper = $this->getSpecificIngredient('poivre');
        $oil = $this->getSpecificIngredient('huile d');
        $sugar = $this->getSpecificIngredient('sugar');
        $onion = $this->getSpecificIngredient('oignon');

        return $onion + $sugar + $oil + $pepper + $beurres;
    }

    private function getDataIngredientInFormatedArray(string $ingredient): array
    {
        $detailsIngredient = explode(':', $ingredient);

        if (!ArrayUtils::checkIfArrayKeyExists($detailsIngredient, 0)) {
            $detailsIngredient[0] = '';
        }

        if (!ArrayUtils::checkIfArrayKeyExists($detailsIngredient, 1)) {
            $detailsIngredient[1] = '';
        }

        return $detailsIngredient;
    }
}