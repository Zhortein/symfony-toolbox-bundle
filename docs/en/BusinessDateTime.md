
# BusinessDateTime Documentation

## Introduction

The `BusinessDateTime` class is used to manage business dates, taking into account specific working days and public holidays for a given country. It enables checking if a given date corresponds to a business day or not and determining which days are holidays in a specific country.

## Properties

### $holidays

```php
/**
 * Stores the current holiday settings.
 *
 * @var array<int, \DateTime>
 */
protected array $holidays = [];
```

This property contains an array of dates representing the current holidays.

### $workingDays

```php
/**
 * Stores the working days of the week.
 *
 * @var array<int, Day>
 */
protected array $workingDays = [Day::MONDAY, Day::TUESDAY, Day::WEDNESDAY, Day::THURSDAY, Day::FRIDAY];
```

This property contains an array of weekdays considered as working days (Monday to Friday by default).

## Constructor

```php
public function __construct(
    protected readonly HolidayProviderManager $holidayProviderManager,
    protected ?LoggerInterface $logger = null,
    protected ?int $currentYear = null,
    protected ?string $currentCountry = null
)
```

The constructor initializes the class with the following parameters:
- `HolidayProviderManager $holidayProviderManager`: A manager for holiday providers.
- `LoggerInterface $logger`: An optional logger for recording information.
- `int $currentYear`: The current year (optional, defaults to the current year if undefined).
- `string $currentCountry`: The current country (optional, defaults to France if undefined).

Note that the current year and country are only used to initialize the tool; you can change them later as needed.

You can choose the logger to use via the `setLogger(LoggerInterface)` method and retrieve the configured logger with `getLogger()`.

## Enumerations

Each enumeration has a `label` method that retrieves the translated label if a `TranslatorInterface` is provided, or the corresponding name within the enumeration.

```php
use Zhortein\SymfonyToolboxBundle\Enum\Day;
use Symfony\Contracts\Translation\TranslatorInterface;

class TestController extends AbstractController 
{
    public function testAction(TranslatorInterface $translator): Response
    {
        $monday = Day::MONDAY;
        
        echo $monday->label($translator);
        // Outputs: Monday
        
        echo $monday->label();
        // Outputs: MONDAY
    }
}
```

### Days of the Week

The `Day` enumeration contains days of the week:
- `Day::MONDAY` = 1
- `Day::TUESDAY` = 2
- `Day::WEDNESDAY` = 3
- `Day::THURSDAY` = 4
- `Day::FRIDAY` = 5
- `Day::SATURDAY` = 6
- `Day::SUNDAY` = 7

### Months of the Year

The `Month` enumeration contains months of the year:
- `Month::JANUARY` = 1
- `Month::FEBRUARY` = 2
- `Month::MARCH` = 3
- `Month::APRIL` = 4
- `Month::MAY` = 5
- `Month::JUNE` = 6
- `Month::JULY` = 7
- `Month::AUGUST` = 8
- `Month::SEPTEMBER` = 9
- `Month::OCTOBER` = 10
- `Month::NOVEMBER` = 11
- `Month::DECEMBER` = 12

## Methods

### Day Check Methods

The following methods allow checking if a given date corresponds to a specific day of the week:

- `isMonday(\DateTimeInterface $myDate): bool`
- `isTuesday(\DateTimeInterface $myDate): bool`
- `isWednesday(\DateTimeInterface $myDate): bool`
- `isThursday(\DateTimeInterface $myDate): bool`
- `isFriday(\DateTimeInterface $myDate): bool`
- `isSaturday(\DateTimeInterface $myDate): bool`
- `isSunday(\DateTimeInterface $myDate): bool`

#### Usage Example

```php
$holidayProviderManager = new HolidayProviderManager();
$logger = null; // or an instance of LoggerInterface
$currentYear = 2023;
$currentCountry = 'FR';

$businessDateTime = new BusinessDateTime($holidayProviderManager, $logger, $currentYear, $currentCountry);

$date = new \DateTime('2023-12-25'); // Example date

if ($businessDateTime->isMonday($date)) {
    echo "December 25, 2023, is a Monday.";
} else {
    echo "December 25, 2023, is not a Monday.";
}
```

### Managing Working Days

By default, the tool considers the working week from Monday to Friday, excluding weekends. You can modify this behavior using the following methods to manage working days:

- `setWorkingDays(int[]) : void` : Pass an array of integers corresponding to working days. These integers follow PHP's `date()` function format.
- `setWorkingDays(Day[]) : void` : Pass an array with values from the "Day" enumeration corresponding to working days.
- `getWorkingDays(bool $asIntegers = false) : array` : Returns an array of working days, either as integers (if `$asIntegers = true`) or as "Day" enumerations.

These working days affect the behavior of the tool, particularly in the following functions:

- `isWorkingDay(\DateTimeInterface $myDate): bool` : Returns `true` if the given date is a working day.
- `addBusinessDays(\DateTimeInterface $myDate, int $nbToAdd): \DateTimeInterface` : Returns the calculated date after adding the specified number of working days to the provided date.
- `getNbBusinessDays(\DateTimeInterface $start, \DateTimeInterface $end): int` : Returns the number of working days between the two provided dates.
- `getBusinessDays(\DateTimeInterface $start, \DateTimeInterface $end): array` : Returns an array of dates representing working days between the two provided dates.

These functions automatically retrieve holidays for additional years if the date range extends beyond the current year.

### Managing Holidays

The tool includes holiday and leave management. Holidays are provided by classes dedicated to a specific country, known as holiday providers.

Available countries (more may be added, or custom providers can be defined):
- FR: France
- BE: Belgium
- EN: England
- US: United States
- ES: Spain
- DE: Germany

To use a holiday provider, simply define the desired dataset by calling the `setHolidays` method. You can also add custom holidays to the holiday list using `addHoliday`.

### Methods

- `setHolidays(\DateTimeInterface|int $myDate, string $countryCode = 'FR'): array` : Initializes the holiday array with holidays for the specified country and year, with the year optionally extracted from the provided `DateTimeInterface`.
- `getHolidays(\DateTimeInterface|int $myDate, string $countryCode = 'FR'): array` : Alias for `setHolidays`.
- `addHoliday(\DateTimeInterface $myDate): array` : Adds a date to the holiday list. Useful for local holidays or application-specific leave days.
- `addHolidaysForYear(int $year): array` : Adds the holidays for the specified year to the current holiday list.
- `isHolidayListEmpty(): bool` : Returns `true` if the holiday list is empty or uninitialized.
- `emptyHolidays(): void` : Clears the holiday list.
- `isHoliday(\DateTimeInterface $myDate, string $countryCode = 'FR'): bool` : Returns `true` if the provided date is a holiday for the specified country. If the country does not match the current settings, the holiday array is recalculated with the new country.

## Creating a Holiday Provider for Other Countries

To create a holiday provider for another country, you must define a class and annotate it with `AsHolidayProvider`.

### Example

```php
<?php

namespace App\HolidayProvider;

use Zhortein\SymfonyToolboxBundle\Annotation\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Provider\HolidayProviderInterface;

#[AsHolidayProvider(country="ES")]
class SpainHolidayProvider implements HolidayProviderInterface
{
    public function getHolidays(int $year): array
    {
        // Return an array of DateTime representing holidays in Spain for the given year
        return [
            new \DateTime("$year-01-01"), // New Year's Day
            new \DateTime("$year-01-06"), // Epiphany
            // Add additional holidays as needed
        ];
    }
}
```

Adding the attribute <code>#[AsHolidayProvider(country="ES")]</code> to the class automatically registers it as a holiday provider for Spain.
