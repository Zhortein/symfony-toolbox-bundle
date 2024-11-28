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
class BelgiumHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays(int $year): array
    {
        $holidays = [
            $this->getHolidayCalculator()->newYear($year), // Jour de l'an
            $this->getHolidayCalculator()->labourDay($year), // Fête du travail
            new \DateTime("$year-07-21"), // Fête nationale
            $this->getHolidayCalculator()->assumptionOfMary($year), // Assomption
            $this->getHolidayCalculator()->allSaintsDay($year), // Toussaint
            new \DateTime("$year-11-11"), // Armistice 1918
            $this->getHolidayCalculator()->christmasDay($year), // Noël
        ];

        // Ajouter les jours fériés basés sur Pâques
        return array_merge($holidays, $this->getHolidayCalculator()->calculateEasterBasedHolidays($year));
    }
}
