<?php

namespace App\Service\Contract;

use App\Entity\Recipe;
use App\Enum\DifficultyEnum;

interface GeneratorRecipeInterface
{
    public function generateRandomRecipe(DifficultyEnum $difficultyEnum): Recipe;
}