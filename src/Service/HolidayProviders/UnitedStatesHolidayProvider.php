<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;

/**
 * Provides a list of public holidays in the United States for a given year.
 */
#[AsHolidayProvider(countryCodes: ['US'])]
class UnitedStatesHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays(int $year): array
    {
        return [
            $this->getHolidayCalculator()->newYear($year), // New Year's Day
            new \DateTime("third Monday of January $year"), // Martin Luther King Jr. Day
            new \DateTime("third Monday of February $year"), // Presidents' Day
            new \DateTime("$year-07-04"), // Independence Day
            new \DateTime("first Monday of September $year"), // Labor Day
            new \DateTime("fourth Thursday of November $year"), // Thanksgiving Day
            $this->getHolidayCalculator()->christmasDay($year), // Christmas Day
        ];
    }
}
