
# Documentation for `ColorTools`

## `ColorTools` Class

The `ColorTools` class provides various methods for manipulating and generating colors.

---

### Properties

#### `$predefinedPalette`

```php
private static array $predefinedPalette = [
    '#9400D3', '#FF4500', '#6E0B14', '#095228', '#696969',
    '#00008B', '#B8860B', '#C60800', '#F0E68C', '#008000',
    '#FFD700', '#003366', '#A10684', '#4682B4', '#FFD700',
    '#00FF7F', '#FA8072', '#4B0082', '#DB7093', '#87CEFA',
    '#1FA055', '#FF69B4', '#B0C4DE', '#8B4513', '#FFDAB9'
];
```

A predefined palette of colors in hexadecimal format.

---

### Methods

#### `generateRandomColor`

```php
public static function generateRandomColor(): string
```

Generates a random color in hexadecimal format.

- **Returns**: A string representing a random color in hexadecimal format.

#### `getContrastingColor`

```php
public static function getContrastingColor(string $hexColor): string
```

Determines the contrasting color (black or white) based on the brightness of a given color.

- **hexColor**: The color in hexadecimal format to analyze.
- **Returns**: `#000000` for black or `#FFFFFF` for white, based on the brightness of `hexColor`.

#### `blendColors`

```php
public static function blendColors(string $color1, string $color2): string
```

Blends two colors and returns the result.

- **color1**: The first color in hexadecimal format.
- **color2**: The second color in hexadecimal format.
- **Returns**: A hexadecimal string representing the blended color.

#### `darkenColor`

```php
public static function darkenColor(string $hexColor, float $percentage): string
```

Darkens a color by a specified percentage.

- **hexColor**: The color in hexadecimal format to darken.
- **percentage**: A float between 0 and 1 representing the darkening percentage.
- **Returns**: The darkened color in hexadecimal format.

#### `lightenColor`

```php
public static function lightenColor(string $hexColor, float $percentage): string
```

Lightens a color by a specified percentage.

- **hexColor**: The color in hexadecimal format to lighten.
- **percentage**: A float between 0 and 1 representing the lightening percentage.
- **Returns**: The lightened color in hexadecimal format.

## Usage Example

```php
$randomColor = ColorTools::generateRandomColor();
echo "Random color: $randomColor";

$contrastingColor = ColorTools::getContrastingColor($randomColor);
echo "Contrasting color: $contrastingColor";

$blendedColor = ColorTools::blendColors('#FF4500', '#1E90FF');
echo "Blended color: $blendedColor";

$darkenedColor = ColorTools::darkenColor('#FFD700', 0.2);
echo "Darkened color: $darkenedColor";

$lightenedColor = ColorTools::lightenColor('#4682B4', 0.3);
echo "Lightened color: $lightenedColor";
```
