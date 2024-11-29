<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Service\MeasureConverter;

class MeasureConverterTest extends TestCase
{
    public function testConvertBytesToHumanReadable(): void
    {
        $this->assertSame('1.00 octets', MeasureConverter::convertBytesToHumanReadable(1, 2, 'fr'));
        $this->assertSame('1.00 Ko', MeasureConverter::convertBytesToHumanReadable(1024, 2, 'fr'));
        $this->assertSame('1.00KB', MeasureConverter::convertBytesToHumanReadable(1024, 2, 'en'));
    }

    public function testConvertHaToSquareKm(): void
    {
        $this->assertEquals(1.0, MeasureConverter::convertHaToSquareKm(100));
    }

    public function testConvertSquareKmToHa(): void
    {
        $this->assertEquals(100.0, MeasureConverter::convertSquareKmToHa(1));
    }

    public function testConvertKmToMiles(): void
    {
        $this->assertEqualsWithDelta(6.21371, MeasureConverter::convertKmToMiles(10), 0.00001);
    }

    public function testConvertMilesToKm(): void
    {
        $this->assertEqualsWithDelta(16.0934, MeasureConverter::convertMilesToKm(10), 0.0001);
    }

    public function testConvertCelsiusToFahrenheit(): void
    {
        $this->assertEquals(32.0, MeasureConverter::convertCelsiusToFahrenheit(0));
        $this->assertEquals(212.0, MeasureConverter::convertCelsiusToFahrenheit(100));
    }

    public function testConvertFahrenheitToCelsius(): void
    {
        $this->assertEquals(0.0, MeasureConverter::convertFahrenheitToCelsius(32));
        $this->assertEquals(100.0, MeasureConverter::convertFahrenheitToCelsius(212));
    }

    public function testConvertKgToPounds(): void
    {
        $this->assertEqualsWithDelta(22.0462, MeasureConverter::convertKgToPounds(10), 0.0001);
    }

    public function testConvertPoundsToKg(): void
    {
        $this->assertEqualsWithDelta(4.53592, MeasureConverter::convertPoundsToKg(10), 0.00001);
    }

    public function testConvertSquareMetersToSquareFeet(): void
    {
        $this->assertEqualsWithDelta(107.639, MeasureConverter::convertSquareMetersToSquareFeet(10), 0.001);
    }

    public function testConvertSquareFeetToSquareMeters(): void
    {
        $this->assertEqualsWithDelta(9.2903, MeasureConverter::convertSquareFeetToSquareMeters(100), 0.0001);
    }

    public function testConvertLitersToGallons(): void
    {
        $this->assertEqualsWithDelta(2.64172, MeasureConverter::convertLitersToGallons(10), 0.00001);
    }

    public function testConvertGallonsToLiters(): void
    {
        $this->assertEqualsWithDelta(37.8541, MeasureConverter::convertGallonsToLiters(10), 0.0001);
    }

    public function testConvertPascalToPsi(): void
    {
        $this->assertEqualsWithDelta(0.145038, MeasureConverter::convertPascalToPsi(1000), 0.000001);
    }

    public function testConvertPsiToPascal(): void
    {
        $this->assertEqualsWithDelta(6894.7448, MeasureConverter::convertPsiToPascal(1), 0.0001);
    }
}
