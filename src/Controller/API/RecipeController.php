<?php

namespace App\Controller\API;

use App\Service\RecipeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RecipeController extends AbstractController
{
    public function __construct(
        private readonly RecipeManager       $recipeManager,
        private readonly SerializerInterface $serializer,
    )
    {
    }

    #[Route('/api/recipes', name: 'api_recipes')]
    public function getRecipes(): JsonResponse
    {
        return $this->json(
            $this->recipeManager->getNewsRecipes(),
            200,
            [],
            ['groups' => 'recipe:read']
        );
    }

}