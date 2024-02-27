<?php

namespace App\Service;

use LLPhant\Chat\OpenAIChat;
use LLPhant\OpenAIConfig;

class OpenAiManager
{
    public function __construct(
        private readonly string $apiKeyOpenAI
    )
    {
    }

    private function getApiKey(): string
    {
        return $this->apiKeyOpenAI;
    }

    /**
     * @throws \Exception
     */
    public function getOpenAiChat(): OpenAIChat
    {
        $config = new OpenAIConfig();
        $config->apiKey = ($this->getApiKey());
        $config->model = 'gpt-3.5-turbo';
        return new OpenAIChat($config);
    }
}