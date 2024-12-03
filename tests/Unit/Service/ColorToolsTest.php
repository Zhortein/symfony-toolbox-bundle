<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Service\ColorTools;

class ColorToolsTest extends TestCase
{
    public function testHexToRgb(): void
    {
        $rgb = ColorTools::hexToRgb('#FFFFFF');
        $this->assertEquals([255, 255, 255], $rgb);

        $rgb = ColorTools::hexToRgb('#000000');
        $this->assertEquals([0, 0, 0], $rgb);

        $rgb = ColorTools::hexToRgb('#FFF');
        $this->assertEquals([255, 255, 255], $rgb);

        $rgb = ColorTools::hexToRgb('#000');
        $this->assertEquals([0, 0, 0], $rgb);

        $this->expectException(\InvalidArgumentException::class);
        ColorTools::hexToRgb('#ZZZZZZ');

        $this->expectException(\InvalidArgumentException::class);
        ColorTools::hexToRgb('#ZZZZZZZZ');

        $this->expectException(\InvalidArgumentException::class);
        ColorTools::hexToRgb('#ABCD');
    }

    public function testRgbToHex(): void
    {
        $hex = ColorTools::rgbToHex([255, 255, 255]);
        $this->assertEquals('#ffffff', $hex);

        $hex = ColorTools::rgbToHex([0, 0, 0]);
        $this->assertEquals('#000000', $hex);

        $this->expectException(\InvalidArgumentException::class);
        ColorTools::rgbToHex([255, 255]);
    }

    public function testIsValidHexColor(): void
    {
        $this->assertTrue(ColorTools::isValidHexColor('#FFFFFF'));
        $this->assertTrue(ColorTools::isValidHexColor('#000000'));
        $this->assertFalse(ColorTools::isValidHexColor('#GGGGGG'));
        $this->assertFalse(ColorTools::isValidHexColor('FFFFFF'));
    }

    public function testMixColors(): void
    {
        $mixed = ColorTools::mixColors('#FF0000', '#0000FF');
        $this->assertEquals('#7f007f', $mixed);

        $mixed = ColorTools::mixColors('#00FF00', '#0000FF');
        $this->assertEquals('#007f7f', $mixed);
    }

    public function testGenerateUniqueColors(): void
    {
        $colors = ColorTools::generateUniqueColors(5);
        $this->assertCount(5, $colors);
        $this->assertIsString($colors[0]);
        $this->assertTrue(ColorTools::isValidHexColor($colors[0]));

        $colors = ColorTools::generateUniqueColors(40);
        $this->assertCount(40, $colors);
    }

    public function testGenerateUniqueColorsPairs(): void
    {
        $pairs = ColorTools::generateUniqueColorsPairs(3);
        $this->assertCount(3, $pairs['vivid']);
        $this->assertCount(3, $pairs['pale']);
        $this->assertNotEmpty($pairs['vivid'][0]);
        $this->assertNotEmpty($pairs['pale'][0]);
        $this->assertTrue(ColorTools::isValidHexColor($pairs['vivid'][0]));
        $this->assertTrue(ColorTools::isValidHexColor($pairs['pale'][0]));
    }

    public function testMakeColorPale(): void
    {
        $paleColor = ColorTools::makeColorPale('#FF0000');
        $this->assertTrue(ColorTools::isValidHexColor($paleColor));
        $this->assertNotEquals('#FF0000', $paleColor);
    }

    public function testIsDistinct(): void
    {
        $distinct = ColorTools::isDistinct([255, 0, 0], ['#0000FF']);
        $this->assertTrue($distinct);

        $distinct = ColorTools::isDistinct([0, 0, 255], ['#0000FF']);
        $this->assertFalse($distinct);
    }

    public function testColorDistance(): void
    {
        $distance = ColorTools::colorDistance([255, 0, 0], [255, 0, 0]);
        $this->assertEquals(0, $distance);

        $distance = ColorTools::colorDistance([255, 0, 0], [0, 0, 255]);
        $this->assertGreaterThan(0, $distance);
    }
}
