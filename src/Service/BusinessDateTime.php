<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Psr\Log\LoggerInterface;
use Zhortein\SymfonyToolboxBundle\Enum\Day;

class BusinessDateTime
{
    /**
     * Store current Holidays settings.
     *
     * @var \DateTimeInterface[]
     */
    protected array $holidays = [];

    /**
     * Store loaded years for the current country.
     *
     * @var int[]
     */
    protected array $yearsLoaded = [];

    /**
     * Store working days of the week.
     *
     * @var Day[]
     */
    protected array $workingDays = [Day::MONDAY, Day::TUESDAY, Day::WEDNESDAY, Day::THURSDAY, Day::FRIDAY];

    public function __construct(
        protected readonly HolidayProviderManager $holidayProviderManager,
        protected ?LoggerInterface $logger = null,
        protected ?int $currentYear = null,
        protected ?string $currentCountry = null,
    ) {
        if (null === $this->currentYear) {
            $this->currentYear = (int) date('Y');
        }
        if (null === $this->currentCountry) {
            $this->currentCountry = 'FR';
        }
        $this->setHolidays($this->currentYear, $this->currentCountry);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Checks if the given date matches the specified day.
     *
     * @param \DateTimeInterface $myDate the date to check
     * @param Day                $day    the day to compare against
     *
     * @return bool returns true if the date matches the specified day, otherwise false
     */
    protected function isDay(\DateTimeInterface $myDate, Day $day): bool
    {
        return $day->value === (int) $myDate->format('N');
    }

    /**
     * Checks if the given date is a Monday.
     *
     * @param \DateTimeInterface $myDate the date to check
     *
     * @return bool returns true if the given date is a Monday, false otherwise
     */
    public function isMonday(\DateTimeInterface $myDate): bool
    {
        return $this->isDay($myDate, Day::MONDAY);
    }

    /**
     * Checks if the given date falls on a Tuesday.
     *
     * @param \DateTimeInterface $myDate the date to be checked
     *
     * @return bool returns true if the date is a Tuesday, otherwise false
     */
    public function isTuesday(\DateTimeInterface $myDate): bool
    {
        return $this->isDay($myDate, Day::TUESDAY);
    }

    /**
     * Checks if the given date falls on a Wednesday.
     *
     * @param \DateTimeInterface $myDate the date to check
     *
     * @return bool returns true if the given date is a Wednesday, false otherwise
     */
    public function isWednesday(\DateTimeInterface $myDate): bool
    {
        return $this->isDay($myDate, Day::WEDNESDAY);
    }

    /**
     * Checks if the given date falls on a Thursday.
     *
     * @param \DateTimeInterface $myDate the date to be checked
     *
     * @return bool returns true if the given date is a Thursday, otherwise false
     */
    public function isThursday(\DateTimeInterface $myDate): bool
    {
        return $this->isDay($myDate, Day::THURSDAY);
    }

    /**
     * Checks if the given date is a Friday.
     *
     * @param \DateTimeInterface $myDate the date to check
     *
     * @return bool returns true if the given date is a Friday, false otherwise
     */
    public function isFriday(\DateTimeInterface $myDate): bool
    {
        return $this->isDay($myDate, Day::FRIDAY);
    }

    /**
     * Checks if the given date falls on a Saturday.
     *
     * @param \DateTimeInterface $myDate the date to be checked
     *
     * @return bool returns true if the date is a Saturday, false otherwise
     */
    public function isSaturday(\DateTimeInterface $myDate): bool
    {
        return $this->isDay($myDate, Day::SATURDAY);
    }

    /**
     * Checks if the given date is a Sunday.
     *
     * @param \DateTimeInterface $myDate the date to check
     *
     * @return bool returns true if the date is a Sunday, otherwise false
     */
    public function isSunday(\DateTimeInterface $myDate): bool
    {
        return $this->isDay($myDate, Day::SUNDAY);
    }

    /**
     * Determines if the given date falls on a weekend (Saturday or Sunday).
     *
     * @param \DateTimeInterface $myDate the date to check
     *
     * @return bool returns true if the date is a Saturday or Sunday, false otherwise
     */
    public function isWeekEnd(\DateTimeInterface $myDate): bool
    {
        return $this->isSunday($myDate) || $this->isSaturday($myDate);
    }

    /**
     * Sets the holidays for the specified year and country.
     *
     * @param \DateTimeInterface|int $myDate      the year or date from which to extract the year
     * @param string                 $countryCode The country code for which holidays are to be set. Default is 'FR'.
     *
     * @return \DateTimeInterface[] returns an array of holidays for the specified year and country
     */
    public function setHolidays(\DateTimeInterface|int $myDate, string $countryCode = 'FR'): array
    {
        $this->emptyHolidays();

        if (!is_int($myDate)) {
            $this->currentYear = (int) $myDate->format('Y');
        } else {
            $this->currentYear = $myDate;
        }
        $this->currentCountry = strtoupper($countryCode);

        $provider = $this->holidayProviderManager->getProvider($countryCode);
        if ($provider) {
            $this->holidays = $provider->getHolidays($this->currentYear);
            $this->yearsLoaded[] = $this->currentYear;
        } else {
            // @todo Traduire
            $this->logger?->warning("Le fournisseur de jours fériés pour le pays {$countryCode} n'est pas disponible.", [
                'title' => 'Missing holiday provider',
                'description' => 'No holiday provider found for country '.$countryCode.'. Try to update the bundle if the requested country is available.',
            ]);
        }

        return $this->getUniqueHolidays();
    }

    /**
     * Adds holidays for the specified year for the current Country, updating the list if the year differs from the current year.
     *
     * @param int $year the year for which holidays should be added
     *
     * @return \DateTimeInterface[] returns an array of holidays for the specified year
     */
    public function addHolidaysForYear(int $year): array
    {
        if ($year !== $this->currentYear && !in_array($year, $this->yearsLoaded, true)) {
            $provider = $this->holidayProviderManager->getProvider($this->currentCountry ?? '');
            if ($provider) {
                $this->holidays = $provider->getHolidays($year);
                $this->yearsLoaded[] = $year;
            } else {
                // @todo Traduire
                $this->logger?->warning(
                    "Le fournisseur de jours fériés pour le pays {$this->currentCountry} n'est pas disponible.",
                    [
                        'title' => 'Missing holiday provider',
                        'description' => 'No holiday provider found for country '.$this->currentCountry.'. Try to update the bundle if the requested country is available.',
                    ]
                );
            }
        }

        return $this->getUniqueHolidays();
    }

    /**
     * Retrieves the list of holidays for a given date and country.
     *
     * @param \DateTimeInterface|int $myDate      The date for which holidays are to be retrieved. Can be a DateTimeInterface object or a Unix timestamp.
     * @param string                 $countryCode The country code for which holidays are to be retrieved. Defaults to 'FR'.
     *
     * @return \DateTimeInterface[] returns an array of holidays for the specified date and country
     */
    public function getHolidays(\DateTimeInterface|int $myDate, string $countryCode = 'FR'): array
    {
        return $this->setHolidays($myDate, $countryCode);
    }

    /**
     * Check if the given date is a holiday in the specified country.
     *
     * @param \DateTimeInterface $myDate      the date to check
     * @param string             $countryCode The country code to check for holidays. Default is 'FR'.
     *
     * @return bool true if the date is a holiday, false otherwise
     */
    public function isHoliday(\DateTimeInterface $myDate, string $countryCode = 'FR'): bool
    {
        $year = (int) $myDate->format('Y');
        $country = strtoupper($countryCode);

        if ($year !== $this->currentYear) {
            $this->addHolidaysForYear($year);
        }

        if ($country !== $this->currentCountry) {
            $this->setHolidays($myDate, $countryCode);
        }

        return in_array(
            $myDate->format('d/m/Y'),
            array_map(static fn ($holiday) => $holiday->format('d/m/Y'), $this->holidays),
            true
        );
    }

    /**
     * Empty the list of known holidays.
     */
    public function emptyHolidays(): void
    {
        $this->holidays = [];
        $this->yearsLoaded = [];
    }

    /**
     * Retrieves the list of years that have been loaded.
     *
     * @return int[] returns an array of years that have been loaded
     */
    public function getYearsLoaded(): array
    {
        return $this->yearsLoaded;
    }

    /**
     * Determines if the specified year is loaded.
     *
     * @param int $year the year to check
     *
     * @return bool returns true if the year is loaded, false otherwise
     */
    public function isYearLoaded(int $year): bool
    {
        return in_array($year, $this->yearsLoaded, true);
    }

    /**
     * Checks if the list of holidays is empty.
     *
     * @return bool returns true if the list of holidays is empty, false otherwise
     */
    public function isHolidayListEmpty(): bool
    {
        return empty($this->holidays);
    }

    /**
     * Renvoie une liste unique de jours fériés.
     *
     * @return \DateTimeInterface[]
     */
    protected function getUniqueHolidays(): array
    {
        $uniqueHolidays = [];
        $hashes = [];

        foreach ($this->holidays as $holiday) {
            $hash = $holiday->format('Y-m-d');

            if (!in_array($hash, $hashes, true)) {
                $hashes[] = $hash;
                $uniqueHolidays[] = $holiday;
            }
        }

        return $uniqueHolidays;
    }

    /**
     * Adds a holiday to the list of known holidays.
     *
     * @param \DateTimeInterface $myDate date of the holiday to be added
     *
     * @return \DateTimeInterface[] the updated list of holidays
     */
    public function addHoliday(\DateTimeInterface $myDate): array
    {
        $this->holidays[] = $myDate;

        return $this->getUniqueHolidays();
    }

    /**
     * Adds a specified number of business days to a given date.
     *
     * @param \DateTimeInterface $myDate  the starting date to which business days will be added
     * @param int                $nbToAdd the number of business days to add
     *
     * @return \DateTimeInterface returns the new date after adding the specified number of business days
     */
    public function addBusinessDays(\DateTimeInterface $myDate, int $nbToAdd): \DateTimeInterface
    {
        $this->currentYear = (int) $myDate->format('Y');

        if ($nbToAdd > 0) {
            $myDate = $this->addBusinessDay($myDate, $nbToAdd);
        }

        return $myDate;
    }

    /**
     * Calculates the number of business days between two dates.
     *
     * @param \DateTimeInterface $start the start date
     * @param \DateTimeInterface $end   the end date
     *
     * @return int returns the number of business days between the start and end dates
     *
     * @throws \DateMalformedStringException
     */
    public function getNbBusinessDays(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $nbBusinessDays = 0;

        // Clone immuable pour préserver les heures originales
        $current = new \DateTimeImmutable($start->format('Y-m-d H:i:s'), $start->getTimezone());
        $endCopy = new \DateTimeImmutable($end->format('Y-m-d H:i:s'), $end->getTimezone());

        $endCopy = $endCopy->add(new \DateInterval('P1D')); // Inclut la date de fin

        while ($current < $endCopy) {
            if (!$this->isWeekEnd($current) && !$this->isHoliday($current)) {
                ++$nbBusinessDays;
            }
            $current = $current->add(new \DateInterval('P1D'));
        }

        return $nbBusinessDays;
    }

    /**
     * Calculates the business days between two dates, excluding weekends and holidays.
     *
     * @param \DateTimeInterface $start the start date
     * @param \DateTimeInterface $end   the end date
     *
     * @return array<int, \DateTimeInterface> returns an array of business days between the start and end dates
     *
     * @throws \DateMalformedStringException
     */
    public function getBusinessDays(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $businessDays = [];

        // Clone immuable pour conserver les heures originales
        $current = new \DateTimeImmutable($start->format('Y-m-d H:i:s'), $start->getTimezone());
        $endCopy = new \DateTimeImmutable($end->format('Y-m-d H:i:s'), $end->getTimezone());

        $endCopy = $endCopy->add(new \DateInterval('P1D')); // Inclut la date de fin

        while ($current < $endCopy) {
            if (!$this->isWeekEnd($current) && !$this->isHoliday($current)) {
                $businessDays[] = $current;
            }
            $current = $current->add(new \DateInterval('P1D'));
        }

        return $businessDays;
    }

    /**
     * Sets the working days for the business calendar.
     *
     * @param Day[]|int[] $days An array of days to be set as working days. The days can be integers, strings, or instances of Day.
     */
    public function setWorkingDays(array $days): void
    {
        $this->workingDays = array_filter(array_map(static function ($day) {
            if (is_int($day)) {
                $day = 0 === $day ? 7 : $day;

                return Day::tryFrom($day);
            }

            return $day;
        }, $days));
    }

    /**
     * Retrieves the working days.
     *
     * @param bool $asIntegers determines if the working days should be returned as integer values
     *
     * @return int[]|Day[] returns an array of working days, either as objects or as their integer values based on the $asIntegers parameter
     */
    public function getWorkingDays(bool $asIntegers = false): array
    {
        if ($asIntegers) {
            return array_map(static function ($day) {
                return $day->value;
            }, $this->workingDays);
        }

        return $this->workingDays;
    }

    /**
     * Determines if the given date is a working day, excluding holidays and weekends.
     *
     * @param \DateTimeInterface $myDate the date to be checked
     *
     * @return bool true if it's a working day, false otherwise
     */
    public function isWorkingDay(\DateTimeInterface $myDate): bool
    {
        $dayOfWeek = (int) $myDate->format('N');
        foreach ($this->workingDays as $workingDay) {
            if (($workingDay->value === $dayOfWeek) && !$this->isHoliday($myDate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds the specified number of business days to the given date.
     *
     * @param \DateTime|\DateTimeImmutable $myDate the initial date from which to add business days
     * @param int                          $nbDays The number of business days to add. Defaults to 1 if not specified.
     *
     * @return \DateTimeInterface the resultant date after adding the specified number of business days
     *
     * @throws \DateMalformedStringException
     */
    private function addBusinessDay(\DateTime|\DateTimeImmutable $myDate, int $nbDays = 1): \DateTimeInterface
    {
        $counter = 0;

        while ($counter < $nbDays) {
            $myDate = $myDate->modify('+1 day');
            if ($this->isWorkingDay($myDate)) {
                ++$counter;
            }
        }

        return $myDate;
    }
}
