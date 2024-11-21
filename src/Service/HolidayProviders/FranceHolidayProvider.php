<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;

/**
 * FranceHolidayProvider is responsible for providing the list of public holidays in France for a given year.
 *
 * This class implements the HolidayProviderInterface and retrieves holidays based on fixed dates and
 * calculations related to Easter.
 */
#[AsHolidayProvider(countryCodes: ['FR'])]
class FranceHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays(int $year): array
    {
        $holidays = [
            $this->holidayCalculator->newYear($year), // Jour de l'an
            $this->holidayCalculator->labourDay($year), // Fête du travail
            new \DateTime("$year-05-08"), // Victoire 1945
            new \DateTime("$year-07-14"), // Fête nationale
            new \DateTime("$year-08-15"), // Assomption
            new \DateTime("$year-11-01"), // Toussaint
            new \DateTime("$year-11-11"), // Armistice 1918
            $this->holidayCalculator->christmasDay($year), // Noël
        ];

        // Ajouter les jours fériés basés sur Pâques
        return array_merge($holidays, $this->holidayCalculator->calculateEasterBasedHolidays($year));
    }
}
