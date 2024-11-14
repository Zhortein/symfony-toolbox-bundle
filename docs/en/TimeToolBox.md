# TimeToolBox

The TimeToolBox service provides practical methods for manipulating and normalizing DateInterval objects in PHP, making complex time interval operations easier. This service simplifies duration calculations, enabling operations such as adding or subtracting periods, as well as converting intervals into various formats.

## Features

- [Current Time in Milliseconds](#current-time-in-milliseconds)
- [Start and End Dates of a Week](#start-and-end-dates-of-a-week)
- [Convert DateInterval to Seconds](#convert-dateinterval-to-seconds)
- [Divide DateInterval](#divide-dateinterval)
- [Add DateInterval](#add-dateinterval)
- [Subtract DateInterval](#subtract-dateinterval)
- [Convert a DateInterval to ISO8601 Format](#convert-a-dateinterval-to-iso8601-format)
- [Normalize an ISO8601 Duration](#normalize-an-iso8601-duration)
- [Check an ISO8601 Duration](#check-an-iso8601-duration)
- [Convert a DateInterval to a Specific Time Unit](#convert-a-dateinterval-to-a-specific-time-unit)

### Current Time in Milliseconds

Returns the current time in milliseconds.

#### Method

```php
public static function getCurrentMicrotime(): float
```
Returns the current time in milliseconds.

#### Example

```php
echo TimeToolBox::getCurrentMicrotime(); // Returns: 1731404120.3913
```

### Start and End Dates of a Week

Returns the start and end dates for a given week.

#### Method

```php
public static function getWeekStartAndEnd(?int $year = null, ?int $week = null, string $format = 'Y-m-d'): array
```
Returns an array with the start and end dates of the specified week, in the specified format.

#### Parameters
- `?int $year = null` : Year, if null the current year is used.
- `?int $week = null` : Week number, if null the current week is used.
- `string $format = 'Y-m-d'` : Format of the returned dates (as per PHP’s date() format).

#### Example

```php
var_export(TimeToolBox::getWeekStartAndEnd(2024, 10));
// Returns: ['start' => '2024-03-04', 'end' => '2024-03-10']
```

### Convert DateInterval to Seconds

Converts a `DateInterval` into seconds.

#### Method

```php
public static function dateIntervalToSeconds(?\DateInterval $dateInterval): int
```
Returns the number of seconds represented by the DateInterval.

#### Parameters
- `?\DateInterval $dateInterval` : The DateInterval to convert.

#### Example

```php
echo TimeToolBox::dateIntervalToSeconds(new \DateInterval('P1D')); // Returns 86400
```

### Divide DateInterval

Divides one `DateInterval` by another and returns the quotient.

#### Method

```php
public static function dateIntervalDivide(\DateInterval $numerator, \DateInterval $denominator): ?float
```
Returns the quotient of the division as a float.

#### Parameters
- `\DateInterval $numerator` : Numerator for the division.
- `\DateInterval $denominator` : Denominator for the division.

#### Example

```php
$num = new \DateInterval('P1D');
$den = new \DateInterval('P2D');
echo TimeToolBox::dateIntervalDivide($num, $den); // Returns : 0.5
```

### Add DateInterval

Adds multiple DateIntervals together.

#### Method

```php
public static function dateIntervalAdd(?\DateInterval ...$intervals): ?\DateInterval
```
Returns the sum of the DateIntervals as a DateInterval.

#### Parameters
- `?\DateInterval ...$intervals` : As many DateIntervals as desired to add together.

#### Example

```php
$dur1 = new \DateInterval('P1D');
$dur2 = new \DateInterval('P2D');
$dur3 = new \DateInterval('PT1H');
$dur4 = new \DateInterval('PT3H');
$total = TimeToolBox::dateIntervalAdd($dur1, $dur2, $dur3, $dur4);
// Returns: DateInterval('P3D4H')
```

### Subtract DateInterval

Subtracts multiple DateIntervals.

#### Method

```php
public static function dateIntervalSub(?\DateInterval ...$intervals): ?\DateInterval
```
Returns the result of the subtraction of DateIntervals as a DateInterval.

#### Parameters
- `?\DateInterval ...$intervals` : As many DateIntervals as desired to subtract.

#### Example

```php
$dur1 = new \DateInterval('P10D');
$dur2 = new \DateInterval('P2D');
$dur3 = new \DateInterval('PT1H');
$dur4 = new \DateInterval('PT3H');
$total = TimeToolBox::dateIntervalSub($dur1, $dur2, $dur3, $dur4);
// Returns: DateInterval('P8D20H')
```

### Convert a DateInterval to ISO8601 Format

Converts a `DateInterval` to [format ISO8601](https://fr.wikipedia.org/wiki/ISO_8601).

#### Method

```php
public static function dateIntervalToIso8601(?\DateInterval $interval): string
```
Returns the DateInterval as an ISO8601 string.

#### Parameters
- `?\DateInterval $interval` : The DateInterval to convert.

#### Example

```php
$interval = new \DateInterval('P2Y4DT6H8M'); // Intervalle de 2 ans, 4 jours, 6 heures, et 8 minutes
$iso8601 = TimeToolBox::dateIntervalToIso8601($interval);

echo $iso8601; // Returns: "P2Y4DT6H8M"
```

### Normalize an ISO8601 Duration

Normalizes an ISO8601 duration.

#### Method

```php
public static function normalizeISO8601Duration(?string $duration): string
```
Returns the normalized duration string in ISO8601 format.

#### Parameters
- `?string $duration` : The string to normalize.

#### Example

```php
$duration = 'P1Y14M2DT3H75M'; // Example duration of 1 year, 14 months, 2 days, 3 hours, and 75 minutes
$normalizedDuration = TimeToolBox::normalizeISO8601Duration($duration);

echo $normalizedDuration; // Returns: "P2Y2M2DT4H15M"

$invalidDuration = 'invalid_format';
$normalizedDuration = TimeToolBox::normalizeISO8601Duration($invalidDuration);
echo $normalizedDuration; // Returns: "PT0S"

$duration = 'P0Y0M15DT48H'; // 15 days and 48 hours
$normalizedDuration = TimeToolBox::normalizeISO8601Duration($duration);

echo $normalizedDuration; // Returns: "P16DT0H"
```

### Check an ISO8601 Duration

Checks if a duration is in ISO8601 format.

#### Method

```php
public static function isISO8601Duration(string $duration): bool
```
Returns true if the string is a valid ISO8601 duration, false otherwise.

#### Parameters
- `string $duration` : The duration string to validate.

#### Example

```php
TimeToolBox::isISO8601Duration('PT1H'); // true
TimeToolBox::isISO8601Duration('1H'); // false
```

### Convert a DateInterval to a Specific Time Unit

Converts a `DateInterval` to a specified time unit.

#### Method

```php
public static function convertDateInterval(\DateInterval $interval, string $unit, float $hoursPerDay = 24): float
```
Returns the converted result in the specified unit. 
Throws an UnsupportedTimeUnitException if the unit is not available.

#### Parameters
- `\DateInterval $interval` : The DateInterval to convert.
- `string $unit` : Desired unit among 'seconds', 'minutes', 'hours', 'days'.
- `float $hoursPerDay = 24` : Number of hours in a day. Useful for handling workdays, e.g., 7 or 8 hours.

#### Example

```php
$dur = new \DateInterval('P1D');
echo TimeToolBox::convertDateInterval($dur, 'hours'); // Returns: 24
echo TimeToolBox::convertDateInterval($dur, 'days'); // Returns: 1
echo TimeToolBox::convertDateInterval($dur, 'days', 8); // Returns: 3
```

## Notes

These features have been developed as needs arose across multiple projects to avoid code redundancy. They will evolve with Symfony and PHP components.