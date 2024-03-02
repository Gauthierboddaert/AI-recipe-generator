<?php

namespace App\Service\Utils;

class ArrayUtils
{
    public static function checkIfArrayKeyExists(array $array, string $key): bool
    {
        return array_key_exists($key, $array);
    }
}