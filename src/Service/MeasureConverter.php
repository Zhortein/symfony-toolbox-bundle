<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

class MeasureConverter
{
    // Conversion constants
    private const int KB_IN_BYTES = 1024;
    private const float HECTARE_TO_SQUARE_KM = 0.01;
    private const float KM_TO_MILES = 0.621371;
    private const float KG_TO_POUNDS = 2.20462;
    private const float SQ_METER_TO_SQ_FEET = 10.7639;
    private const float LITERS_TO_GALLONS = 0.264172;
    private const float PASCAL_TO_PSI = 0.000145038;

    /**
     * Convertit un nombre d'octets en une chaîne lisible avec des unités adaptées.
     */
    public static function convertBytesToHumanReadable(int $size, int $precision = 2, ?string $locale = null): string
    {
        if ($size < 0) {
            throw new \InvalidArgumentException('La taille ne peut pas être négative.');
        }

        $locale = $locale ?? (extension_loaded('intl') ? \Locale::getDefault() : 'en');
        $language = extension_loaded('intl') ? \Locale::getPrimaryLanguage($locale) : 'en';

        $base = $size > 0 ? log($size, self::KB_IN_BYTES) : 0;

        $units = match ($language) {
            'fr' => ['octets', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo'],
            'de' => ['Byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'],
            default => ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'],
        };

        $humanReadable = round(self::KB_IN_BYTES ** ($base - floor($base)), $precision);
        $separator = match ($language) {
            'fr', 'es' => ' ',
            default => '',
        };

        return sprintf('%.'.$precision.'f%s%s', $humanReadable, $separator, $units[(int) floor($base)]);
    }

    /**
     * Convertit des hectares en kilomètres carrés.
     */
    public static function convertHaToSquareKm(float|int $value): float
    {
        return $value * self::HECTARE_TO_SQUARE_KM;
    }

    /**
     * Convertit des kilomètres carrés en hectares.
     */
    public static function convertSquareKmToHa(float|int $value): float
    {
        return $value / self::HECTARE_TO_SQUARE_KM;
    }

    public static function convertKmToMiles(float|int $km): float
    {
        return $km * self::KM_TO_MILES;
    }

    public static function convertMilesToKm(float|int $miles): float
    {
        return $miles / self::KM_TO_MILES;
    }

    public static function convertCelsiusToFahrenheit(float|int $celsius): float
    {
        return ($celsius * 9 / 5) + 32;
    }

    public static function convertFahrenheitToCelsius(float|int $fahrenheit): float
    {
        return ($fahrenheit - 32) * 5 / 9;
    }

    public static function convertKgToPounds(float|int $kg): float
    {
        return $kg * self::KG_TO_POUNDS;
    }

    public static function convertPoundsToKg(float|int $pounds): float
    {
        return $pounds / self::KG_TO_POUNDS;
    }

    public static function convertSquareMetersToSquareFeet(float|int $sqMeters): float
    {
        return $sqMeters * self::SQ_METER_TO_SQ_FEET;
    }

    public static function convertSquareFeetToSquareMeters(float|int $sqFeet): float
    {
        return $sqFeet / self::SQ_METER_TO_SQ_FEET;
    }

    public static function convertLitersToGallons(float|int $liters): float
    {
        return $liters * self::LITERS_TO_GALLONS;
    }

    public static function convertGallonsToLiters(float|int $gallons): float
    {
        return $gallons / self::LITERS_TO_GALLONS;
    }

    public static function convertPascalToPsi(float|int $pascal): float
    {
        return $pascal * self::PASCAL_TO_PSI;
    }

    public static function convertPsiToPascal(float|int $psi): float
    {
        return $psi / self::PASCAL_TO_PSI;
    }
}
