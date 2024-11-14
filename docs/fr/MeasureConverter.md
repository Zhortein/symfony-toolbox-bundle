# Documentation de `MeasureConverter`

## Classe `MeasureConverter`

La classe `MeasureConverter` fournit des fonctions pour convertir diverses unités de mesure.

### Méthodes

#### `convertBytesToHumanReadable(int $size, int $precision = 2, ?string $locale = null): string`
Convertit un nombre d'octets en une chaîne lisible avec des unités adaptées.

- **Paramètres :**
    - `int $size`: la taille en octets à convertir.
    - `int $precision`: la précision du résultat.
    - `?string $locale`: la locale utilisée pour les unités.

- **Retourne :**
    - `string`: La taille en format lisible.

- **Exception :**
    - `\InvalidArgumentException`: si la taille est négative.

```php
public static function convertBytesToHumanReadable(int $size, int $precision = 2, ?string $locale = null): string
```

#### `convertHaToSquareKm(float|int $value): float`
Convertit des hectares en kilomètres carrés.

- **Paramètre :**
    - `float|int $value`: le nombre d'hectares.

- **Retourne :**
    - `float`: La surface en kilomètres carrés.

```php
public static function convertHaToSquareKm(float|int $value): float
```

#### `convertSquareKmToHa(float|int $value): float`
Convertit des kilomètres carrés en hectares.

- **Paramètre :**
    - `float|int $value`: la surface en kilomètres carrés.

- **Retourne :**
    - `float`: La surface en hectares.

```php
public static function convertSquareKmToHa(float|int $value): float
```

#### `convertKmToMiles(float|int $km): float`
Convertit des kilomètres en miles.

- **Paramètre :**
    - `float|int $km`: le nombre de kilomètres.

- **Retourne :**
    - `float`: La distance en miles.

```php
public static function convertKmToMiles(float|int $km): float
```

#### `convertMilesToKm(float|int $miles): float`
Convertit des miles en kilomètres.

- **Paramètre :**
    - `float|int $miles`: la distance en miles.

- **Retourne :**
    - `float`: La distance en kilomètres.

```php
public static function convertMilesToKm(float|int $miles): float
```

#### `convertCelsiusToFahrenheit(float|int $celsius): float`
Convertit des degrés Celsius en Fahrenheit.

- **Paramètre :**
    - `float|int $celsius`: la température en Celsius.

- **Retourne :**
    - `float`: La température en Fahrenheit.

```php
public static function convertCelsiusToFahrenheit(float|int $celsius): float
```

#### `convertFahrenheitToCelsius(float|int $fahrenheit): float`
Convertit des degrés Fahrenheit en Celsius.

- **Paramètre :**
    - `float|int $fahrenheit`: la température en Fahrenheit.

- **Retourne :**
    - `float`: La température en Celsius.

```php
public static function convertFahrenheitToCelsius(float|int $fahrenheit): float
```

#### `convertKgToPounds(float|int $kg): float`
Convertit des kilogrammes en livres.

- **Paramètre :**
    - `float|int $kg`: le poids en kilogrammes.

- **Retourne :**
    - `float`: Le poids en livres.

```php
public static function convertKgToPounds(float|int $kg): float
```

#### `convertPoundsToKg(float|int $pounds): float`
Convertit des livres en kilogrammes.

- **Paramètre :**
    - `float|int $pounds`: le poids en livres.

- **Retourne :**
    - `float`: Le poids en kilogrammes.

```php
public static function convertPoundsToKg(float|int $pounds): float
```

#### `convertSquareMetersToSquareFeet(float|int $sqMeters): float`
Convertit des mètres carrés en pieds carrés.

- **Paramètre :**
    - `float|int $sqMeters`: la surface en mètres carrés.

- **Retourne :**
    - `float`: La surface en pieds carrés.

```php
public static function convertSquareMetersToSquareFeet(float|int $sqMeters): float
```

#### `convertSquareFeetToSquareMeters(float|int $sqFeet): float`
Convertit des pieds carrés en mètres carrés.

- **Paramètre :**
    - `float|int $sqFeet`: la surface en pieds carrés.

- **Retourne :**
    - `float`: La surface en mètres carrés.

```php
public static function convertSquareFeetToSquareMeters(float|int $sqFeet): float
```

#### `convertLitersToGallons(float|int $liters): float`
Convertit des litres en gallons.

- **Paramètre :**
    - `float|int $liters`: le volume en litres.

- **Retourne :**
    - `float`: Le volume en gallons.

```php
public static function convertLitersToGallons(float|int $liters): float
```

#### `convertGallonsToLiters(float|int $gallons): float`
Convertit des gallons en litres.

- **Paramètre :**
    - `float|int $gallons`: le volume en gallons.

- **Retourne :**
    - `float`: Le volume en litres.

```php
public static function convertGallonsToLiters(float|int $gallons): float
```

#### `convertPascalToPsi(float|int $pascal): float`
Convertit des pascals en psi.

- **Paramètre :**
    - `float|int $pascal`: la pression en pascals.

- **Retourne :**
    - `float`: La pression en psi.

```php
public static function convertPascalToPsi(float|int $pascal): float
```

#### `convertPsiToPascal(float|int $psi): float`
Convertit des psi en pascals.

- **Paramètre :**
    - `float|int $psi`: la pression en psi.

- **Retourne :**
    - `float`: La pression en pascals.

```php
public static function convertPsiToPascal(float|int $psi): float
```

### Exemples d'utilisation

```php
use Zhortein\SymfonyToolboxBundle\Service\MeasureConverter;

try {
    $humanReadableSize = MeasureConverter::convertBytesToHumanReadable(2048);
    echo $humanReadableSize; // Affiche "2.00 KB"

    $hectares = MeasureConverter::convertSquareKmToHa(5);
    echo $hectares; // Affiche "500"

    $miles = MeasureConverter::convertKmToMiles(10);
    echo $miles; // Affiche "6.21371"
    
    $liters = MeasureConverter::convertGallonsToLiters(5);
    echo $liters; // Affiche "18.9271"
    
} catch (\InvalidArgumentException $e) {
    echo 'Erreur: ' . $e->getMessage();
}
```