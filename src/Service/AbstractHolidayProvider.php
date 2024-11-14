<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\HolidayCalculator;

/**
 * Interface for providing holiday dates for a given year.
 */
abstract class AbstractHolidayProvider
{
    public function __construct(protected readonly HolidayCalculator $holidayCalculator)
    {
    }

    /**
     * Retrieves the list of holidays for the specified year.
     *
     * @param int $year the year for which to retrieve the holidays
     *
     * @return \DateTimeInterface[] an array of holidays for the given year
     */
    abstract public function getHolidays(int $year): array;
}
