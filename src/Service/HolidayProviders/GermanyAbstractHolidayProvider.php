<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;

/**
 * GermanyHolidayProvider class implements the HolidayProviderInterface to provide
 * public holidays for Germany, including static and Easter-based holidays.
 */
#[AsHolidayProvider(countryCodes: ['DE'])]
class GermanyAbstractHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays(int $year): array
    {
        $holidays = [
            $this->holidayCalculator->newYear($year), // Neujahrstag
            $this->holidayCalculator->labourDay($year), // Tag der Arbeit
            new \DateTime("$year-10-03"), // Tag der Deutschen Einheit
            $this->holidayCalculator->christmasDay($year), // Erster Weihnachtstag
            $this->holidayCalculator->goodFriday($year), // Erster Weihnachtstag
            new \DateTime("$year-12-26"), // Zweiter Weihnachtstag
        ];

        // Ajouter les jours fériés basés sur Pâques
        return array_merge(array_filter($holidays), $this->holidayCalculator->calculateEasterBasedHolidays($year));
    }
}
