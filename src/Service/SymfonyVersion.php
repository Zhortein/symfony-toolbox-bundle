<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Symfony\Component\HttpKernel\Kernel;

class SymfonyVersion
{
    /**
     * Determines if the current Symfony version is 7.2.0 or higher.
     *
     * @return bool True if the Symfony version is 7.2.0 or higher, false otherwise.
     */
    public static function isSymfony72OrHigher(): bool
    {
        return Kernel::VERSION_ID >= 70200;
    }

    /**
     * Determines if the current Symfony version is equal to or higher than the specified version.
     *
     * @param string $version the version to compare against
     *
     * @return bool true if the current Symfony version is equal to or higher, false otherwise
     */
    public static function isSymfonyHigherOrEgal(string $version): bool
    {
        return version_compare(Kernel::VERSION, $version, '>=');
    }

    /**
     * Checks if the current Symfony version is higher than the specified version.
     *
     * @param string $version the version to compare against
     *
     * @return bool true if the current Symfony version is higher, false otherwise
     */
    public static function isSymfonyHigher(string $version): bool
    {
        return version_compare(Kernel::VERSION, $version, '>');
    }

    /**
     * Compares the current Symfony version against a specified version.
     *
     * This method determines if the current version of Symfony is less than to the provided version string.
     *
     * @param string $version the version to compare against the current Symfony version
     *
     * @return bool returns true if the current Symfony version is less than to the specified version, false otherwise
     */
    public static function isSymfonyLower(string $version): bool
    {
        return version_compare(Kernel::VERSION, $version, '<');
    }

    /**
     * Compares the current Symfony version against a specified version.
     *
     * This method determines if the current version of Symfony is less than or equal to the provided version string.
     *
     * @param string $version the version to compare against the current Symfony version
     *
     * @return bool returns true if the current Symfony version is less than or equal to the specified version, false otherwise
     */
    public static function isSymfonyLowerOrEgal(string $version): bool
    {
        return version_compare(Kernel::VERSION, $version, '<=');
    }
}
