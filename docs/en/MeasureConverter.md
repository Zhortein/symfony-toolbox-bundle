# Documentation for `MeasureConverter`

## Class `MeasureConverter`

The `MeasureConverter` class provides functions to convert various measurement units.

### Methods

#### `convertBytesToHumanReadable(int $size, int $precision = 2, ?string $locale = null): string`
Converts a number of bytes into a human-readable string with appropriate units.

- **Parameters:**
    - `int $size`: the size in bytes to convert.
    - `int $precision`: the precision of the result.
    - `?string $locale`: the locale used for units.

- **Returns:**
    - `string`: The size in a readable format.

- **Exception:**
    - `\InvalidArgumentException`: if the size is negative.

```php
public static function convertBytesToHumanReadable(int $size, int $precision = 2, ?string $locale = null): string
```

#### `convertHaToSquareKm(float|int $value): float`
Converts hectares to square kilometers.

- **Parameter:**
    - `float|int $value`: the number of hectares.

- **Returns:**
    - `float`: The area in square kilometers.

```php
public static function convertHaToSquareKm(float|int $value): float
```

#### `convertSquareKmToHa(float|int $value): float`
Converts square kilometers to hectares.

- **Parameter:**
    - `float|int $value`: the area in square kilometers.

- **Returns:**
    - `float`: The area in hectares.

```php
public static function convertSquareKmToHa(float|int $value): float
```

#### `convertKmToMiles(float|int $km): float`
Converts kilometers to miles.

- **Parameter:**
    - `float|int $km`: the number of kilometers.

- **Returns:**
    - `float`: The distance in miles.

```php
public static function convertKmToMiles(float|int $km): float
```

#### `convertMilesToKm(float|int $miles): float`
Converts miles to kilometers.

- **Parameter:**
    - `float|int $miles`: the distance in miles.

- **Returns:**
    - `float`: The distance in kilometers.

```php
public static function convertMilesToKm(float|int $miles): float
```

#### `convertCelsiusToFahrenheit(float|int $celsius): float`
Converts degrees Celsius to Fahrenheit.

- **Parameter:**
    - `float|int $celsius`: the temperature in Celsius.

- **Returns:**
    - `float`: The temperature in Fahrenheit.

```php
public static function convertCelsiusToFahrenheit(float|int $celsius): float
```

#### `convertFahrenheitToCelsius(float|int $fahrenheit): float`
Converts degrees Fahrenheit to Celsius.

- **Parameter:**
    - `float|int $fahrenheit`: the temperature in Fahrenheit.

- **Returns:**
    - `float`: The temperature in Celsius.

```php
public static function convertFahrenheitToCelsius(float|int $fahrenheit): float
```

#### `convertKgToPounds(float|int $kg): float`
Converts kilograms to pounds.

- **Parameter:**
    - `float|int $kg`: the weight in kilograms.

- **Returns:**
    - `float`: The weight in pounds.

```php
public static function convertKgToPounds(float|int $kg): float
```

#### `convertPoundsToKg(float|int $pounds): float`
Converts pounds to kilograms.

- **Parameter:**
    - `float|int $pounds`: the weight in pounds.

- **Returns:**
    - `float`: The weight in kilograms.

```php
public static function convertPoundsToKg(float|int $pounds): float
```

#### `convertSquareMetersToSquareFeet(float|int $sqMeters): float`
Converts square meters to square feet.

- **Parameter:**
    - `float|int $sqMeters`: the area in square meters.

- **Returns:**
    - `float`: The area in square feet.

```php
public static function convertSquareMetersToSquareFeet(float|int $sqMeters): float
```

#### `convertSquareFeetToSquareMeters(float|int $sqFeet): float`
Converts square feet to square meters.

- **Parameter:**
    - `float|int $sqFeet`: the area in square feet.

- **Returns:**
    - `float`: The area in square meters.

```php
public static function convertSquareFeetToSquareMeters(float|int $sqFeet): float
```

#### `convertLitersToGallons(float|int $liters): float`
Converts liters to gallons.

- **Parameter:**
    - `float|int $liters`: the volume in liters.

- **Returns:**
    - `float`: The volume in gallons.

```php
public static function convertLitersToGallons(float|int $liters): float
```

#### `convertGallonsToLiters(float|int $gallons): float`
Converts gallons to liters.

- **Parameter:**
    - `float|int $gallons`: the volume in gallons.

- **Returns:**
    - `float`: The volume in liters.

```php
public static function convertGallonsToLiters(float|int $gallons): float
```

#### `convertPascalToPsi(float|int $pascal): float`
Converts Pascals to psi.

- **Parameter:**
    - `float|int $pascal`: the pressure in Pascals.

- **Returns:**
    - `float`: The pressure in psi.

```php
public static function convertPascalToPsi(float|int $pascal): float
```

#### `convertPsiToPascal(float|int $psi): float`
Converts psi to Pascals.

- **Parameter:**
    - `float|int $psi`: the pressure in psi.

- **Returns:**
    - `float`: The pressure in Pascals.

```php
public static function convertPsiToPascal(float|int $psi): float
```

### Usage Examples

```php
use Zhortein\SymfonyToolboxBundle\Service\MeasureConverter;

try {
    $humanReadableSize = MeasureConverter::convertBytesToHumanReadable(2048);
    echo $humanReadableSize; // Outputs "2.00 KB"

    $hectares = MeasureConverter::convertSquareKmToHa(5);
    echo $hectares; // Outputs "500"

    $miles = MeasureConverter::convertKmToMiles(10);
    echo $miles; // Outputs "6.21371"
    
    $liters = MeasureConverter::convertGallonsToLiters(5);
    echo $liters; // Outputs "18.9271"
    
} catch (\InvalidArgumentException $e) {
    echo 'Error: ' . $e->getMessage();
}
```