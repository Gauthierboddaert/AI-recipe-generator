<?php

namespace App\Factory;

use App\Entity\Contract\EntityInterface;
use App\Entity\Ingredient;

class IngredientFactory
{
    public static function create(string $name): EntityInterface
    {
        return (new Ingredient())
            ->setName($name);
    }
}