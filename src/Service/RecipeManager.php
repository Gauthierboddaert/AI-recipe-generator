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
        private OpenAiHttp                      $openAiHttp,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function createRecipe(string $name, string $description): Recipe
    {
        /** @var Recipe $recipe */
        $recipe = RecipeFactory::create($name, $description);

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        return $recipe;
    }

    /**
     * @throws \Exception
     */
    public function generateRandomRecipe(DifficultyEnum $difficultyEnum): Recipe
    {
        $prompt = PrompDtoFactory::create(
            prompt: 'Je veux que tu me donnes un nom de recette de difficulté ' . $difficultyEnum->value . 'et  je veux une recette différente de la liste de recette suivante : (chaque recette est séparée par un ;)' . $this->getRecipesName(),
            message: 'donne moi un nom de recette, je veux uniquement un nom de recette, rien d autre'
        );

        return $this->generate($prompt);
    }

    /**
     * @throws \Exception
     */
    private function generate(PromptDto $dto): Recipe
    {
        $recipeName = $this->openAiHttp->request($dto);

        return $this->createRecipe($recipeName, $this->getDescription($recipeName));
    }

    private function getDescription(string $name): string
    {
        $prompt = PrompDtoFactory::create(
            'Je veux que tu me donnes une description pour la recette ' . $name,
            'donne moi une description original pour la recette ' . $name
        );

        return $this->openAiHttp->request($prompt);
    }

    private function getRecipesName(): string
    {
        $recipes = $this->entityManager->getRepository(Recipe::class)->findAll();

        return implode(';', array_map(fn(Recipe $recipe) => $recipe->getName(), $recipes));
    }
}