<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Psr\Log\LoggerInterface;
use Zhortein\SymfonyToolboxBundle\Enum\Day;

class DateToolBox
{
    protected static ?\DateTimeZone $timezone = null;
    protected static ?LoggerInterface $logger = null;

    public static function setLogger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    public static function getLogger(): ?LoggerInterface
    {
        return self::$logger;
    }

    /**
     * Logs a warning message with a predefined format.
     *
     * @param string $message the warning message to be logged
     */
    private static function logWarning(string $message): void
    {
        // @todo Traduire
        self::$logger?->warning($message, [
            'title' => $message,
            'description' => 'Une opération a échoué dans '.__CLASS__,
        ]);
    }

    /**
     * Sets the application's timezone.
     *
     * @param \DateTimeZone|string $timezone the timezone identifier to be set
     */
    public static function setTimeZone(\DateTimeZone|string $timezone): void
    {
        if ($timezone instanceof \DateTimeZone) {
            self::$timezone = $timezone;
        } else {
            try {
                self::$timezone = new \DateTimeZone($timezone);
            } catch (\DateInvalidTimeZoneException) {
                self::$timezone = null;
            }
        }
    }

    /**
     * Retrieves the current timezone setting.
     *
     * @return \DateTimeZone the current timezone if set, or 'Europe/Paris' as the default
     */
    public static function getTimeZone(): \DateTimeZone
    {
        return self::$timezone ?? new \DateTimeZone('Europe/Paris');
    }

    /**
     * Convert an Excel date to a DateTime object.
     *
     * @param float|int|string|null $excelDate the Excel date to convert
     *
     * @return \DateTime|null the converted DateTime object or null if conversion fails
     */
    public static function getDateFromExcel(float|int|string|null $excelDate): ?\DateTime
    {
        if (class_exists('PhpOffice\PhpSpreadsheet\Shared\Date')) {
            if (is_string($excelDate)) {
                $excelDate = (float) trim(str_replace(',', '.', $excelDate));
            }

            if (!empty($excelDate)) {
                try {
                    /** @var \DateTime|null $date */
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelDate, self::getTimeZone());
                } catch (\Exception $e) {
                    try {
                        $formattedDate = str_replace('/', '-', (string) $excelDate);
                        $date = new \DateTime($formattedDate, self::getTimeZone());
                    } catch (\Exception $e) {
                        $date = null;
                    }
                }
            } else {
                $date = null;
            }

            return $date;
        }

        // @todo Traduire
        self::logWarning('PhpOffice\PhpSpreadsheet non installé, impossible de convertir la date Excel en DateTime.');

        return null;
    }

    /**
     * Checks if the provided localized day name matches the day represented by the given date.
     *
     * @param \IntlDateFormatter $formatter the formatter used to localize the day name
     * @param \DateTime          $date      the date to be formatted
     * @param string             $name      the localized name to match against
     *
     * @return bool returns true if the localized day name matches the provided name, false otherwise
     */
    private static function matchesDayName(\IntlDateFormatter $formatter, \DateTime $date, string $name): bool
    {
        $formatter->setPattern('eeee');
        $localizedDayName = strtolower($formatter->format($date) ?: '');
        if ($localizedDayName === $name) {
            return true;
        }

        // Vérifie aussi les cas de suffixes ou de variations de format
        $formatter->setPattern('eee');

        return strtolower($formatter->format($date) ?: '') === $name || str_starts_with(strtolower($formatter->format($date) ?: ''), $name);
    }

    /**
     * Converts a localized day name to its corresponding Day enum.
     *
     * @param string $name   the localized name of the day
     * @param string $locale The locale to be used for the conversion. Default is 'fr'.
     *
     * @return Day|null returns the corresponding Day enum or null if no match is found
     */
    public static function getDayEnumFromName(string $name, string $locale = 'fr'): ?Day
    {
        if (!class_exists('IntlDateFormatter')) {
            // @todo Traduire
            self::logWarning('ext_intl non installé pour obtenir les noms des jours.');

            return null;
        }

        $name = strtolower(trim($name));
        $dayMap = [
            1 => Day::MONDAY,
            2 => Day::TUESDAY,
            3 => Day::WEDNESDAY,
            4 => Day::THURSDAY,
            5 => Day::FRIDAY,
            6 => Day::SATURDAY,
            7 => Day::SUNDAY,
        ];

        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, null, null, 'eeee');

        foreach ($dayMap as $dayIndex => $dayEnum) {
            $date = new \DateTime("Sunday +$dayIndex days");
            if (self::matchesDayName($formatter, $date, $name)) {
                return $dayEnum;
            }
        }

        return null;
    }

    /**
     * Generates a list of the last N months formatted according to a specified pattern.
     *
     * @param int    $nbMonths the number of months to include in the list
     * @param string $format   Optional. The format pattern to use for the month representation. Defaults to 'n/Y'.
     *
     * @return string[] an array containing the last N months in reverse order, formatted according to the specified pattern
     */
    public static function getLastMonthsList(int $nbMonths, string $format = 'n/Y'): array
    {
        $months = [];
        $currentDate = new \DateTimeImmutable();

        for ($i = 0; $i < $nbMonths; ++$i) {
            $months[] = $currentDate->format($format);
            $currentDate = $currentDate->modify('-1 month');
        }

        return array_reverse($months);
    }

    /**
     * Get the list of last months between two given dates.
     *
     * @param \DateTimeInterface $start  the start date
     * @param \DateTimeInterface $end    the end date
     * @param string             $format the format of the months (default: 'n/Y')
     *
     * @return string[] the list of last months formatted according to $format
     */
    public static function getLastMonthsListBetween(\DateTimeInterface $start, \DateTimeInterface $end, string $format = 'n/Y'): array
    {
        $interval = $start->diff($end);
        $months = $interval->y * 12 + $interval->m;
        if ($interval->d > 0) {
            ++$months;
        }

        return self::getLastMonthsList($months, $format);
    }

    /**
     * Get the list of months between two given dates.
     *
     * @param \DateTime|\DateTimeImmutable|null $dateStart the start date (default: null, which means current date)
     * @param \DateTime|\DateTimeImmutable|null $dateEnd   the end date (default: null, which means empty array will be returned)
     * @param string                            $format    the format of the months (default: 'n/Y')
     *
     * @return string[] the list of months formatted according to $format in reverse chronological order
     *
     * @throws \DateMalformedStringException
     */
    public static function getMonthsListBetweenDates(\DateTime|\DateTimeImmutable|null $dateStart = null, \DateTime|\DateTimeImmutable|null $dateEnd = null, string $format = 'n/Y'): array
    {
        $months = [];
        if (null !== $dateEnd) {
            $currentDate = clone $dateEnd;
        } else {
            return $months;
        }

        while ($currentDate > $dateStart) {
            $months[] = $currentDate->format($format);
            $currentDate = $currentDate->modify('-1 month');
        }

        return array_reverse($months);
    }
}
