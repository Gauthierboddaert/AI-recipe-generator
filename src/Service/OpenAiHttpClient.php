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
        //$config->model = 'text-embedding-3-small';
        return new OpenAIChat($config);
    }

    private function getApiKey(): string
    {
        return $this->apiKeyOpenAI;
    }
}