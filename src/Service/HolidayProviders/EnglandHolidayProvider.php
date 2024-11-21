<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;

/**
 * A class that provides holiday dates specific to England.
 *
 * This class implements the HolidayProviderInterface and provides a list of holidays for a given year.
 * The holidays included are:
 * - New Year's Day
 * - Spring Bank Holiday (last Monday of May)
 * - Summer Bank Holiday (last Monday of August)
 * - Christmas Day
 * - Boxing Day
 */
#[AsHolidayProvider(countryCodes: ['EN', 'GB', 'UK'])]
class EnglandHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays(int $year): array
    {
        return [
            $this->holidayCalculator->newYear($year), // New Year's Day
            new \DateTime("last Monday of May $year"), // Spring Bank Holiday
            new \DateTime("last Monday of August $year"), // Summer Bank Holiday
            $this->holidayCalculator->christmasDay($year), // Christmas Day
            new \DateTime("$year-12-26"), // Boxing Day
        ];
    }
}
