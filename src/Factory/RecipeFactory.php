<?php

namespace App\Factory;

use App\Entity\Contract\EntityInterface;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Enum\StatusEnum;

class RecipeFactory
{
    public static function create(string $name, string $description, int $numberOfPersons = 1): EntityInterface
    {
        return (new Recipe())
            ->setName($name)
            ->setDescription($description)
            ->setStatusEnum(StatusEnum::NEED_VALIDATION)
            ->setNumberPerson($numberOfPersons)
            ->setCreated(new \DateTime())
            ->setUpdated(new \DateTime());
    }
}