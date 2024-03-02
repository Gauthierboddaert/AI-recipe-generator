<?php

namespace App\Service;

use App\DTO\IngredientsRecipeDto;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Factory\RecipeDetailsFactory;
use Doctrine\ORM\EntityManagerInterface;

class RecipeDetailsManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function createRecipeDetails(Recipe $recipe, IngredientsRecipeDto $ingredient): void
    {
        if (null !== $ingredients = $this->em->getRepository(Ingredient::class)->findOneBy(['name' => $ingredient->ingredients])) {
            $recipeDetails = RecipeDetailsFactory::create($recipe, $ingredients, $ingredient->quantity);
            $this->em->persist($recipeDetails);
        }
    }
}