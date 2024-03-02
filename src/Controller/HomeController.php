<?php

namespace App\Controller;

use App\Enum\DifficultyEnum;
use App\Factory\PrompDtoFactory;
use App\Service\OpenAiHttpClient;
use App\Service\RecipeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly OpenAiHttpClient $openAiHttpClient,
    )
    {
    }

    /**
     * @throws \Exception
     */
    #[Route('/', name: 'home')]
    public function index(): JsonResponse
    {

        return $this->json('Hello World!');
    }
}