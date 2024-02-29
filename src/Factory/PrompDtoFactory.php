<?php

namespace App\Factory;

use App\DTO\PromptDto;

class PrompDtoFactory
{
    public static function create(string $prompt, string $message): PromptDto
    {
        return (new PromptDto(
            prompt: $prompt,
            systemMessage: $message
        ));
    }
}