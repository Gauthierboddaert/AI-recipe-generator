<?php

namespace App\Service;


use App\Entity\Recipe;
use App\Enum\StatusEnum;
use App\Factory\PrompDtoFactory;
use Doctrine\ORM\EntityManagerInterface;

class IngredientManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OpenAiHttpClient       $openAiHttp,
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

    public function generateIngredientsForSpecificRecipe(Recipe $recipe): void
    {
        $prompt = PrompDtoFactory::create(
            prompt: 'En te basant ' . $recipe->getName() . '?',
            message: 'Voici ma recette : ' . $recipe->getName() . ' en te basant sur les ingredient suivants, génère moi une liste d\'ingrédients pour cette recette : ',
        );

        $this->openAiHttp->request($prompt);
    }
}