<?php

namespace App\Service;

use App\Entity\Recipe;

class RecipeDetailsManager
{
    public function createRecipeDetails(Recipe $recipe, array $ingredients): void
    {
        dd($recipe, $ingredients);
    }
}