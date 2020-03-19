<?php

namespace App\Enums;

use ReflectionClass;

abstract class Enum
{
    /**
     * Returns all constant values
     *
     * @return array $constantValues
     */
    public static function getAll(): array
    {
        return array_values((new ReflectionClass(static::class))->getConstants());
    }
}
