<?php

namespace App\Factory;

use App\Entity\Recipe;
use App\Entity\RecipeLog;
use App\Enum\StatusEnum;

class RecipeLogFactory
{
    public static function create(Recipe $recipe, string $ingredientPromptResult, array $missedIngredients = []): RecipeLog
    {
        return (new RecipeLog())
            ->setRecipe($recipe)
            ->setStatusEnum(StatusEnum::TO_REVIEW)
            ->setIngredientsPrompt($ingredientPromptResult)
            ->setIngredientsMissed($missedIngredients)
            ->setCreated(new \DateTimeImmutable())
            ->setUpdated(new \DateTimeImmutable());

    }

}