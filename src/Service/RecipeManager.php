<?php

namespace App\Service;

use App\DTO\PromptDto;
use App\Entity\Recipe;
use App\Enum\DifficultyEnum;
use App\Factory\PrompDtoFactory;
use App\Factory\RecipeFactory;
use App\Service\Contract\GeneratorRecipeInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class RecipeManager implements GeneratorRecipeInterface
{
    public function __construct(
        private OpenAiHttpClient       $openAiHttp,
        private EntityManagerInterface $entityManager,
        private IngredientManager      $ingredientManager,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function createRecipe(string $name, string $description, array $ingredients = []): Recipe
    {
        /** @var Recipe $recipe */
        $recipe = RecipeFactory::create($name, $description);
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        if (count($ingredients) > 0) {
            $this->ingredientManager->setIngredientsToRecipe($recipe, $ingredients);
        }

        return $recipe;
    }

    /**
     * @throws \Exception
     */
    public function generateRandomRecipe(DifficultyEnum $difficultyEnum): Recipe
    {
        $randomIngredients = implode(';', $this->ingredientManager->getRandomIngredientsForCreatingRecipe());

        $prompt = PrompDtoFactory::create(
            prompt: 'Je veux que tu me donnes un nom de recette qui existe selon de grand chef de cuisine et de difficulté ' . $difficultyEnum->value . 'et  je veux une recette française qui utilises certains ingrédients que je vais te donner. Les ingrédients sont ' . $randomIngredients . ' et je veux que tu me donnes une recette qui utilise ces ingrédients. Je veux que tu me donnes un nom de recette, je veux uniquement un nom de recette, rien d autre',
            message: 'donne moi un nom de recette, je veux uniquement un nom de recette, rien d autre',
            options: [$randomIngredients]
        );

        return $this->generate($prompt);
    }

    /**
     * @throws \Exception
     */
    private function generate(PromptDto $dto): Recipe
    {
        $recipeName = $this->openAiHttp->request($dto);

        return $this->createRecipe(
            name: $recipeName,
            description: $this->getDescription($recipeName),
            ingredients: $dto->options
        );
    }

    /**
     * @throws \Exception
     */
    private function getDescription(string $name): string
    {
        $prompt = PrompDtoFactory::create(
            'Je veux que tu me donnes une description (max 500 mots) pour la recette ' . $name,
            'donne moi une description original pour la recette ' . $name
        );

        return $this->openAiHttp->request($prompt);
    }

    private function getRecipesName(): string
    {
        $recipes = $this->entityManager->getRepository(Recipe::class)->findAll();

        if (empty($recipes)) {
            return '';
        }

        return implode(';', array_map(fn(Recipe $recipe) => $recipe->getName(), $recipes));
    }

    /** @return Recipe[] */
    public function getNewsRecipes(): array
    {
        return $this->entityManager->getRepository(Recipe::class)->getRecipesWithNewsStatus();
    }
}