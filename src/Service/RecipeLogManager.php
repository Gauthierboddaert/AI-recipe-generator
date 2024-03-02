<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Enum\StatusEnum;
use App\Factory\RecipeLogFactory;
use Doctrine\ORM\EntityManagerInterface;

class RecipeLogManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function createRecipeLog(Recipe $recipe, string $ingredientPromptResult, array $missedIngredients, bool $needFlush = false): void
    {
        $recipe->setStatusEnum(StatusEnum::TO_REVIEW);
        $recipeLog = RecipeLogFactory::create($recipe, $ingredientPromptResult, $missedIngredients);

        $this->em->persist($recipeLog);

        if ($needFlush) {
            $this->em->flush();
        }
    }

}