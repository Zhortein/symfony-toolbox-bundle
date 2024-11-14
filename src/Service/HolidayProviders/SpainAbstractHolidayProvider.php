<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;

#[AsHolidayProvider(['ES'])]
class SpainAbstractHolidayProvider extends AbstractHolidayProvider
{
    public function getHolidays($year): array
    {
        return array_filter([
            $this->holidayCalculator->newYear($year),
            $this->holidayCalculator->epiphany($year),
            $this->holidayCalculator->labourDay($year),
            $this->holidayCalculator->christmasDay($year),
            $this->holidayCalculator->holyThursday($year),
            $this->holidayCalculator->goodFriday($year),
            $this->holidayCalculator->allSaintsDay($year),
            $this->holidayCalculator->assumptionOfMary($year),
            $this->holidayCalculator->immaculateConception($year),
            new \DateTime("$year-10-12"), // National Day
            new \DateTime("$year-12-06"), // Constitution Day
        ]);
    }
}
