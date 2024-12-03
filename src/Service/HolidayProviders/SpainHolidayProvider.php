<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;

#[AsHolidayProvider(['ES'])]
class SpainHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays($year): array
    {
        return array_filter([
            $this->getHolidayCalculator()->newYear($year),
            $this->getHolidayCalculator()->epiphany($year),
            $this->getHolidayCalculator()->labourDay($year),
            $this->getHolidayCalculator()->christmasDay($year),
            $this->getHolidayCalculator()->holyThursday($year),
            $this->getHolidayCalculator()->goodFriday($year),
            $this->getHolidayCalculator()->allSaintsDay($year),
            $this->getHolidayCalculator()->assumptionOfMary($year),
            $this->getHolidayCalculator()->immaculateConception($year),
            new \DateTime("$year-10-12"), // National Day
            new \DateTime("$year-12-06"), // Constitution Day
        ]);
    }
}
