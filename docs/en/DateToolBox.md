
# Documentation for `DateToolBox`

## `DateToolBox` Class

The `DateToolBox` class provides various methods for handling dates, managing time zones, and working with localized day names using advanced features like `IntlDateFormatter`. It also allows setting a logger for recording diagnostic messages.

---

### Properties

#### `$timezone`

```php
protected static ?\DateTimeZone $timezone = null;
```

Stores the currently defined timezone for the application. By default, `Europe/Paris` is used if no timezone is specified.

#### `$logger`

```php
protected static ?LoggerInterface $logger = null;
```

Stores an instance of `LoggerInterface` for managing log messages.

---

### Methods

#### `setLogger`

```php
public static function setLogger(LoggerInterface $logger): void
```

Sets the logger for recording diagnostic messages.

- **logger** : An instance of `LoggerInterface` used for logs.

#### `getLogger`

```php
public static function getLogger(): ?LoggerInterface
```

Retrieves the current logger instance.

- **Returns** : The defined logger or `null` if not configured.

#### `logWarning`

```php
private static function logWarning(string $message): void
```

Private method to log a warning message with a predefined format.

- **message** : The warning message to be logged.

#### `setTimeZone`

```php
public static function setTimeZone(\DateTimeZone|string $timezone): void
```

Sets the application's timezone. If a string identifier is provided, it is converted to `DateTimeZone`.

- **timezone** : The timezone as a `DateTimeZone` object or string identifier.

#### `getTimeZone`

```php
public static function getTimeZone(): \DateTimeZone
```

Retrieves the application's current timezone. Defaults to `Europe/Paris` if none has been set.

- **Returns** : The `DateTimeZone` instance corresponding to the current timezone.

#### `getDateFromExcel`

```php
public static function getDateFromExcel(mixed $excelDate): ?\DateTime
```

Converts an Excel date to a `DateTime` object.

- **excelDate** : The Excel date to convert.
- **Returns** : The converted `DateTime` object or `null` if conversion fails.

#### `getDayEnumFromName`

```php
public static function getDayEnumFromName(string $name, string $locale = 'fr'): ?Day
```

Converts a localized day name to its corresponding `Day` enum (BackedEnum).

- **name** : The localized day name.
- **locale** : The language used for conversion. Defaults to `fr`.
- **Returns** : The corresponding `Day` enum or `null` if no match is found.

#### `getLastMonthsList`

```php
public static function getLastMonthsList(int $nbMonths, string $format = 'n/Y'): array
```

Generates a list of the last N months in a specific format.

- **nbMonths** : The number of months to include in the list.
- **format** : The display format for the months (default `n/Y`).
- **Returns** : An array of the last months in reverse order.

#### `getLastMonthsListBetween`

```php
public static function getLastMonthsListBetween(\DateTimeInterface $start, \DateTimeInterface $end, string $format = 'n/Y'): array
```

Generates a list of months between two dates.

- **start** : The start date.
- **end** : The end date.
- **format** : The format of the months (default `n/Y`).
- **Returns** : An array of formatted months between the two dates.

#### `getMonthsListBetweenDates`

```php
public static function getMonthsListBetweenDates(?\DateTimeInterface $dateStart = null, ?\DateTimeInterface $dateEnd = null, string $format = 'n/Y'): array
```

Generates a list of months between two dates in reverse chronological order.

- **dateStart** : The start date (default: current date).
- **dateEnd** : The end date (default: returns an empty array if not defined).
- **format** : The format of the months (default `n/Y`).
- **Returns** : An array of formatted months between the two dates, in reverse chronological order.

---
