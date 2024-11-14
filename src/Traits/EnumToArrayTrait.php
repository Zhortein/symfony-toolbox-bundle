<?php

namespace Zhortein\SymfonyToolboxBundle\Traits;

trait EnumToArrayTrait
{
    /**
     * Get an array with Enum names.
     *
     * @return array<int, string> enums names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get an array with Enum values.
     *
     * @return array<int, int|string> enums values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Return Enum as Array.
     * If Enum names are empty, a simple array with values will be returned,
     * if Enum values are empty, a simple array with names will be returned,
     * otherwise an array with names and values is returned.
     *
     * @param bool $valuesAsKeys false to have Enum names as keys, true for Enum values as keys
     *
     * @return array<int|string, int|string>
     */
    public static function asArray(bool $valuesAsKeys = false): array
    {
        if (empty(self::values())) {
            return self::names();
        }

        if (empty(self::names())) {
            return self::values();
        }

        return $valuesAsKeys ? array_column(self::cases(), 'name', 'value') : array_column(self::cases(), 'value', 'name');
    }
}
