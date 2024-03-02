<?php

namespace App\Factory;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeDetail;

class RecipeDetailsFactory
{
    public static function create(Recipe $recipe, Ingredient $ingredient, string $quantity): RecipeDetail
    {
        return (new RecipeDetail())
            ->addRecipe($recipe)
            ->setIngredient($ingredient)
            ->setQuantity($quantity);
    }
}