<?php

namespace App\Controller;

use App\Enum\DifficultyEnum;
use App\Service\RecipeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly RecipeManager $recipeManager,
    )
    {
    }

    /**
     * @throws \Exception
     */
    #[Route('/', name: 'home')]
    public function index(): JsonResponse
    {
        //Todo currently set value in the manager, but it should be dynamic based on the request
        $this->recipeManager->generateRandomRecipe(DifficultyEnum::EASY);
        return $this->json('Hello World!');
    }
}