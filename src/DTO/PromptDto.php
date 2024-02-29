<?php

namespace App\DTO;
class PromptDto
{
    public function __construct(
        public readonly string $prompt,
        public readonly string $systemMessage,
    )
    {
    }
}