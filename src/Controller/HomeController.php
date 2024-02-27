<?php

namespace App\Controller;

use App\Service\OpenAiHttp;
use App\Service\OpenAiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly OpenAiHttp $openAiHttp,
    )
    {
    }

    #[Route('/', name: 'home')]
    public function index(): JsonResponse
    {
        $this->openAiHttp->request();
        return $this->json('Hello World!');
    }
}