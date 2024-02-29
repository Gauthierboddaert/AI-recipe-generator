<?php

namespace App\Factory;

use App\DTO\IngredientsRecipeDto;
use App\Entity\Contract\EntityInterface;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Enum\StatusEnum;

class IngredientsRecipeDtoFactory
{
    //Quantity is a string because API can return number or int
    public static function create(string $ingredientName, string $quantity): IngredientsRecipeDto
    {
        return (new IngredientsRecipeDto(
            ingredients: $ingredientName,
            quantity: $quantity,
        ));
    }
}