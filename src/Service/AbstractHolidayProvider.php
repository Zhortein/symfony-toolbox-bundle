<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\HolidayCalculator;

/**
 * Interface for providing holiday dates for a given year.
 */
abstract class AbstractHolidayProvider
{
    public function __construct(protected ?HolidayCalculator $holidayCalculator = null)
    {
        if (null === $this->holidayCalculator) {
            $this->holidayCalculator = new HolidayCalculator();
        }
    }

    public function __toString(): string
    {
        return get_class($this);
    }

    public function setHolidayCalculator(HolidayCalculator $holidayCalculator): void
    {
        $this->holidayCalculator = $holidayCalculator;
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
