<?php

namespace App\Service;

use App\DTO\PromptDto;
use LLPhant\Chat\FunctionInfo\FunctionInfo;
use LLPhant\Chat\OpenAIChat;
use LLPhant\OpenAIConfig;

readonly class OpenAiHttpClient
{

    public function __construct(
        private string $apiKeyOpenAI,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function request(PromptDto $dto): string
    {
        $chat = $this->getOpenAiChat();
        //$chat->setSystemMessage('Tu es un expert de la cuisine et je veux que tu me listes les ingrédients pour faire des recettes. Quand tu me listes des ingrédients, je veux que tu me donnes forcément le nombre de gramme, et je ne veux pas de environ, je veux un poids fixe');
        //$response = $chat->generateText('voici une liste d ingrédient sépraré par un ;,je veux que tu me créer une recette en me listant uniquement les ingrédients et en listant le nombre de gramme, fait moi une recette avec: tomate; oignon; ail; poivron; huile; sel; poivre; eau;');

        $chat->setSystemMessage($dto->systemMessage);
        return $chat->generateText($dto->prompt);
    }

    /**
     * @throws \Exception
     */
    private function getOpenAiChat(): OpenAIChat
    {
        $config = new OpenAIConfig();
        $config->apiKey = ($this->getApiKey());
        $config->model = 'gpt-3.5-turbo';
        return new OpenAIChat($config);
    }

    private function getApiKey(): string
    {
        return $this->apiKeyOpenAI;
    }
}