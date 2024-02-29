<?php

namespace App\DTO;

class IngredientsRecipeDto
{
    public function __construct(
        public string $ingredients,
        public string    $quantity,
    )
    {
    }
}