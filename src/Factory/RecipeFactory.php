<?php

namespace App\Factory;

use App\Entity\Contract\EntityInterface;
use App\Entity\Ingredient;
use App\Entity\Recipe;

class RecipeFactory
{
    public static function create(string $name, string $description): EntityInterface
    {
        return (new Recipe())
            ->setName($name)
            ->setDescription($description);
    }
}