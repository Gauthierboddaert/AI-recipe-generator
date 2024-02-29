<?php

namespace App\Service;


use App\DTO\IngredientsRecipeDto;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Factory\IngredientsRecipeDtoFactory;
use App\Factory\PrompDtoFactory;
use Doctrine\ORM\EntityManagerInterface;

class IngredientManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OpenAiHttpClient       $openAiHttp,
        private readonly RecipeDetailsManager   $recipeDetailsManager,
    )
    {
    }

    public function setIngredientsToRecipeWithStateNew(): void
    {
        /** @var Recipe[] $recipes */
        $recipes = $this->entityManager->getRepository(Recipe::class)->getRecipesWithoutIngredients();
        $i = 0;


        foreach ($recipes as $recipe) {

            $this->generateIngredientsForSpecificRecipe($recipe);

            if ($i % 100 === 0) {
                $this->clearEntityManager();
            }
            $i++;
        }
    }

    private function clearEntityManager(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * @throws \Exception
     */
    public function generateIngredientsForSpecificRecipe(Recipe $recipe): void
    {
        //TODO select most important ingredients in recipe and then select 1000 ingredients random 
        $order = 'Dans ta réponse, je veux que tu listes uniquement les ingrédients, je te demande par exemple une tarte au citron.
        Ta réponse va être Citron : 10g; Sucre : 100g; Farine : 200g; Beurre : 100g; Oeuf : 2. Aussi si tu as un ingrédient comme des pommes de terre, des pommes, ne donne pas un nombre mais toujours un le nombre de gramme si possible.
        Enfin si tu as des ingredients comme pomme, fait attention de bien récupérer les ingredients en base comme Pomme, pulpe et peau, crue, c est très important que j ai le nom exact.
        En te basant sur cette recette ' . $recipe->getName() . ' fournis moi les ingrédients nécessaire que
        je t ai fournis. Je veux que tu utilises absolument à la lettre près les ingrédients que je t ai fournis.';

        $prompt = PrompDtoFactory::create(
            prompt: $order,
            message: 'En te basant sur les ingredient suivants que je sépare par un ;,Je veux que tu utilises absolument à la lettre près les ingrédients que je t ai fournis et uniquement ceux là, je veux que tu me les listes et que tu m indiques combien de gramme je dois mettre, génère moi une liste d\'ingrédients pour cette recette : ' . $this->getIngredients(),
        );

        $response = $this->openAiHttp->request($prompt);

        $this->handleDataIngredientsPrompted($recipe, $response);

    }

    private function handleDataIngredientsPrompted(Recipe $recipe, string $ingredients): void
    {
        $ingredientsDto = $this->formatDataIngredientsPromptedInDto($ingredients);

        foreach ($ingredientsDto as $ingredientDto) {
            if (!$this->checkIfIngredientExist($ingredientDto->ingredients)) {
                throw new \Exception('Ingredient ' . $ingredientDto->ingredients . ' does not exist');
            }
        }

        $this->recipeDetailsManager->createRecipeDetails($recipe, $ingredientsDto);
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
            $detailsIngredient = explode(':', $ingredient);

            $ingredientsDto[] = IngredientsRecipeDtoFactory::create(
                ingredientName: $detailsIngredient[0],
                quantity: $detailsIngredient[1],
            );
        }

        return $ingredientsDto;
    }

    private function getIngredients(): string
    {
        $ingredients = $this->entityManager->getRepository(Ingredient::class)->findAll();

        return implode(';', array_map(fn(Ingredient $ingredient) => $ingredient->getName(), $ingredients));
    }
}