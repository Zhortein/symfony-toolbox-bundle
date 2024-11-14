<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

class ColorTools
{
    /**
     * @var string[]
     */
    private static array $predefinedPalette = [
        '#9400D3', '#FF4500', '#6E0B14', '#095228', '#696969',
        '#00008B', '#B8860B', '#C60800', '#F0E68C', '#008000',
        '#FFD700', '#003366', '#A10684', '#4682B4', '#FFD700',
        '#00FF7F', '#FA8072', '#4B0082', '#DB7093', '#87CEFA',
        '#1FA055', '#FFD700', '#FF1493', '#5A5E6B', '#FFDAB9',
        '#9ACD32', '#00BFFF', '#BF3030', '#FFD700', '#C60800',
        '#00FF7F', '#689D71', '#2C75FF', '#FF00FF', '#F4661B',
        '#FFDAB9', '#FFFF00',
    ];

    /**
     * Convert a hexadecimal color code to its RGB components.
     *
     * @return array<int, float|int>
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (!preg_match('/^[0-9A-Fa-f]{3}$|^[0-9A-Fa-f]{6}$/', $hex)) {
            throw new \InvalidArgumentException('Invalid hex color');
        }

        if (3 === strlen($hex)) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Convert an RGB color to a hexadecimal color code.
     *
     * @param int[] $rgb
     */
    public static function rgbToHex(array $rgb): string
    {
        if (3 !== count($rgb)) {
            throw new \InvalidArgumentException('Invalid RGB color');
        }

        return strtolower(sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]));
    }

    /**
     * Validates if a given string is a valid hex color.
     */
    public static function isValidHexColor(string $color): bool
    {
        return 1 === preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
    }

    /**
     * Mix two colors and return the resulting color.
     */
    public static function mixColors(string $color1, string $color2): string
    {
        list($r1, $g1, $b1) = self::hexToRgb($color1);
        list($r2, $g2, $b2) = self::hexToRgb($color2);

        $r = ($r1 + $r2) / 2;
        $g = ($g1 + $g2) / 2;
        $b = ($b1 + $b2) / 2;

        return self::rgbToHex([(int) $r, (int) $g, (int) $b]);
    }

    /**
     * Generate unique colors.
     *
     * @return string[]
     */
    public static function generateUniqueColors(int $count): array
    {
        if ($count <= count(self::$predefinedPalette)) {
            return array_slice(self::$predefinedPalette, 0, $count);
        }

        $colors = [];
        while (count($colors) < $count) {
            $color = sprintf('#%06X', random_int(0, 0xFFFFFF));
            if (self::isDistinct(self::hexToRgb($color), $colors)) {
                $colors[] = $color;
            }
        }

        return $colors;
    }

    /**
     * Check if a color is distinct from an array of existing colors.
     *
     * @param array<int, float|int> $rgb
     * @param string[]              $colors
     */
    public static function isDistinct(array $rgb, array $colors): bool
    {
        foreach ($colors as $existingColor) {
            if (self::colorDistance($rgb, self::hexToRgb($existingColor)) < 50) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the distance between two RGB color values.
     *
     * @param array<int, float|int> $rgb1
     * @param array<int, float|int> $rgb2
     */
    public static function colorDistance(array $rgb1, array $rgb2): float
    {
        return sqrt((($rgb2[0] - $rgb1[0]) ** 2) + (($rgb2[1] - $rgb1[1]) ** 2) + (($rgb2[2] - $rgb1[2]) ** 2));
    }

    /**
     * Generate unique color pairs.
     *
     * @return array<string, array<string>>
     */
    public static function generateUniqueColorsPairs(int $count): array
    {
        $vividColors = ($count <= count(self::$predefinedPalette)) ?
            array_slice(self::$predefinedPalette, 0, $count) :
            self::generateUniqueColors($count);

        $paleColors = array_map([self::class, 'makeColorPale'], $vividColors);

        return ['vivid' => $vividColors, 'pale' => $paleColors];
    }

    /**
     * Generate a pale version of the given color.
     */
    public static function makeColorPale(string $color): string
    {
        $rgb = self::hexToRgb($color);

        return self::rgbToHex([
            (int) min(255, $rgb[0] + (255 - $rgb[0]) * 0.5),
            (int) min(255, $rgb[1] + (255 - $rgb[1]) * 0.5),
            (int) min(255, $rgb[2] + (255 - $rgb[2]) * 0.5),
        ]);
    }
}
