<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;

/**
 * Class providing holiday dates for Belgium.
 *
 * This class implements the HolidayProviderInterface to return an array
 * of holiday dates for the specified year.
 */
#[AsHolidayProvider(countryCodes: ['BE'])]
class BelgiumAbstractHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays(int $year): array
    {
        $holidays = [
            $this->holidayCalculator->newYear($year), // Jour de l'an
            $this->holidayCalculator->labourDay($year), // Fête du travail
            new \DateTime("$year-07-21"), // Fête nationale
            $this->holidayCalculator->assumptionOfMary($year), // Assomption
            $this->holidayCalculator->allSaintsDay($year), // Toussaint
            new \DateTime("$year-11-11"), // Armistice 1918
            $this->holidayCalculator->christmasDay($year), // Noël
        ];

        // Ajouter les jours fériés basés sur Pâques
        return array_merge($holidays, $this->holidayCalculator->calculateEasterBasedHolidays($year));
    }
}
