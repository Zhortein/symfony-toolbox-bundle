# TimeToolBox

Le service TimeToolBox offre des méthodes pratiques pour manipuler et normaliser les objets DateInterval en PHP, facilitant ainsi les opérations complexes impliquant des intervalles de temps. Ce service simplifie les calculs de durées, permettant des opérations telles que l’ajout ou la soustraction de périodes, ainsi que la conversion des intervalles en différents formats.

## Fonctionnalités

- [Heure actuelle en millisecondes](#heure-actuelle-en-millisecondes)
- [Dates de début et fin d'une semaine](#dates-de-début-et-fin-dune-semaine)
- [Convertir DateInterval en secondes](#convertir-dateinterval-en-secondes)
- [Division de DateInterval](#division-de-dateinterval)
- [Addition de DateInterval](#addition-de-dateinterval)
- [Soustraction de DateInterval](#soustraction-de-dateinterval)
- [Convertit un DateInterval en format ISO8601](#convertit-un-dateinterval-en-format-iso8601)
- [Normaliser une durée au format ISO8601](#normaliser-une-durée-au-format-iso8601)
- [Vérifier une durée au format ISO8601](#vérifier-une-durée-au-format-iso8601)
- [Convertir un DateInterval en une unité de temps](#convertir-un-dateinterval-en-une-unité-de-temps)

### Heure actuelle en millisecondes

Retourne l'heure actuelle en millisecondes.

#### Méthode

```php
public static function getCurrentMicrotime(): float
```
Retourne l'heure actuelle en millisecondes.

#### Exemple

```php
echo TimeToolBox::getCurrentMicrotime(); // Retourne : 1731404120.3913
```

### Dates de début et fin d'une semaine

Retourne les dates de début et de fin pour une semaine donnée.

#### Méthode

```php
public static function getWeekStartAndEnd(?int $year = null, ?int $week = null, string $format = 'Y-m-d'): array
```
Renvoie un tableau avec les dates de début et de fin de la semaine demandée, dans le format demandé.

#### Paramètres
- `?int $year = null` : Année, si null l'année courante est prise en compte
- `?int $week = null` : Numéro de semaine, si null la semaine courante est prise en compte
- `string $format = 'Y-m-d'` : Format des dates retournées (selon les formats date() de PHP)

#### Exemple

```php
var_export(TimeToolBox::getWeekStartAndEnd(2024, 10));
// Retourne : ['start' => '2024-03-04', 'end' => '2024-03-10']
```

### Convertir DateInterval en secondes

Convertit un `DateInterval` en nombre de secondes.

#### Méthode

```php
public static function dateIntervalToSeconds(?\DateInterval $dateInterval): int
```
Retourne le nombre de secondes représenté par le DateInterval.

#### Paramètres
- `?\DateInterval $dateInterval` : Le DateInterval à convertir.

#### Exemple

```php
echo TimeToolBox::dateIntervalToSeconds(new \DateInterval('P1D')); // Retourne 86400
```

### Division de DateInterval

Divise un `DateInterval` par un autre et renvoie le quotient.

#### Méthode

```php
public static function dateIntervalDivide(\DateInterval $numerator, \DateInterval $denominator): ?float
```
Retourne le quotient de la division sous forme de float.

#### Paramètres
- `\DateInterval $numerator` : Numérateur de la division
- `\DateInterval $denominator` : Dénominateur de la division

#### Exemple

```php
$num = new \DateInterval('P1D');
$den = new \DateInterval('P2D');
echo TimeToolBox::dateIntervalDivide($num, $den); // Returns : 0.5
```

### Addition de DateInterval

Additionne plusieurs DateInterval.

#### Méthode

```php
public static function dateIntervalAdd(?\DateInterval ...$intervals): ?\DateInterval
```
Renvoie la somme des DateInterval sous forme d'un DateInterval.

#### Paramètres
- `?\DateInterval ...$intervals` : Autant de DateInterval que souhaité à additionner

#### Exemple

```php
$dur1 = new \DateInterval('P1D');
$dur2 = new \DateInterval('P2D');
$dur3 = new \DateInterval('PT1H');
$dur4 = new \DateInterval('PT3H');
$total = TimeToolBox::dateIntervalAdd($dur1, $dur2, $dur3, $dur4);
// Retourne : DateInterval('P3D4H')
```

### Soustraction de DateInterval

Soustrait plusieurs DateInterval.

#### Méthode

```php
public static function dateIntervalSub(?\DateInterval ...$intervals): ?\DateInterval
```
Renvoie la soustraction des DateInterval sous forme d'un DateInterval.

#### Paramètres
- `?\DateInterval ...$intervals` : Autant de DateInterval que souhaité à soustraire

#### Exemple

```php
$dur1 = new \DateInterval('P10D');
$dur2 = new \DateInterval('P2D');
$dur3 = new \DateInterval('PT1H');
$dur4 = new \DateInterval('PT3H');
$total = TimeToolBox::dateIntervalSub($dur1, $dur2, $dur3, $dur4);
// Retourne : DateInterval('P8D20H')
```

### Convertit un DateInterval en format ISO8601

Convertit un DateInterval en [format ISO8601](https://fr.wikipedia.org/wiki/ISO_8601).

#### Méthode

```php
public static function dateIntervalToIso8601(?\DateInterval $interval): string
```
Renvoie un DateInterval sous forme d'une chaîne ISO8601.

#### Paramètres
- `?\DateInterval $interval` : Le DateInterval à convertir.

#### Exemple

```php
$interval = new \DateInterval('P2Y4DT6H8M'); // Intervalle de 2 ans, 4 jours, 6 heures, et 8 minutes
$iso8601 = TimeToolBox::dateIntervalToIso8601($interval);

echo $iso8601; // Retourne : "P2Y4DT6H8M"
```

### Normaliser une durée au format ISO8601

Normalise une durée au format ISO8601.

#### Méthode

```php
public static function normalizeISO8601Duration(?string $duration): string
```
Renvoie la chaîne de la durée normalisée au format ISO8601.

#### Paramètres
- `?string $duration` : La chaîne à normaliser.

#### Exemple

```php
$duration = 'P1Y14M2DT3H75M'; // Exemple de durée de 1 an, 14 mois, 2 jours, 3 heures et 75 minutes
$normalizedDuration = TimeToolBox::normalizeISO8601Duration($duration);

echo $normalizedDuration; // Retourne : "P2Y2M2DT4H15M"

$invalidDuration = 'invalid_format';
$normalizedDuration = TimeToolBox::normalizeISO8601Duration($invalidDuration);
echo $normalizedDuration; // Retourne : "PT0S"

$duration = 'P0Y0M15DT48H'; // 15 jours et 48 heures
$normalizedDuration = TimeToolBox::normalizeISO8601Duration($duration);

echo $normalizedDuration; // Retourne : "P16DT0H"
```

### Vérifier une durée au format ISO8601

Vérifie si une durée est au format ISO8601.

#### Méthode

```php
public static function isISO8601Duration(string $duration): bool
```
Retourne true si la chaîne est bien une durée valide au format ISO8601, false sinon.

#### Paramètres
- `string $duration` : La chaîne de durée à contrôler.

#### Exemple

```php
TimeToolBox::isISO8601Duration('PT1H'); // true
TimeToolBox::isISO8601Duration('1H'); // false
```

### Convertir un DateInterval en une unité de temps

Convertit un `DateInterval` en une unité de temps spécifique.

#### Méthode

```php
public static function convertDateInterval(\DateInterval $interval, string $unit, float $hoursPerDay = 24): float
```
Résultat de la conversion dans l'unité demandée.
Renvoie une Exception UnsupportedTimeUnitException si l'unité n'est pas disponible.

#### Paramètres
- `\DateInterval $interval` : Le DateInterval à convertir.
- `string $unit` : L'unité souhaitée parmi 'seconds', 'minutes', 'hours', 'days'.
- `float $hoursPerDay = 24` : Nombre d'heures dans une journée. Peut surprendre, mais s'avère utile quand on considère des jours de travail par exemple de 7 ou 8 heures.

#### Exemple

```php
$dur = new \DateInterval('P1D');
echo TimeToolBox::convertDateInterval($dur, 'hours'); // Retourne : 24
echo TimeToolBox::convertDateInterval($dur, 'days'); // Retourne : 1
echo TimeToolBox::convertDateInterval($dur, 'days', 8); // Retourne : 3
```

## Notes

Ces fonctionnalités ont été créées au fil des besoins sur de multiples projets, pour éviter les redondances de code.
Elles évolueront avec les composants de Symfony et de PHP.