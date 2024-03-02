<?php

namespace App\Service\Utils;

class StringUtils
{
    public static function removeFirstSpaceAndlastOfstring(string $text): string
    {
        $text = ltrim($text);
        return rtrim($text);
    }
}