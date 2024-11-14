<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\BelgiumAbstractHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\EnglandAbstractHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\FranceAbstractHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\GermanyAbstractHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\HolidayCalculator;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\SpainAbstractHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\UnitedStatesAbstractHolidayProvider;

/**
 * Factory class for creating holiday provider instances based on the country code.
 */
readonly class HolidayProviderFactory
{
    public function __construct(
        protected HolidayCalculator $holidayCalculator,
    ) {
    }

    public function create(string $countryCode): ?AbstractHolidayProvider
    {
        return match (strtoupper($countryCode)) {
            'FR' => new FranceAbstractHolidayProvider($this->holidayCalculator),
            'BE' => new BelgiumAbstractHolidayProvider($this->holidayCalculator),
            'EN','GB','UK' => new EnglandAbstractHolidayProvider($this->holidayCalculator),
            'US' => new UnitedStatesAbstractHolidayProvider($this->holidayCalculator),
            'DE' => new GermanyAbstractHolidayProvider($this->holidayCalculator),
            'ES' => new SpainAbstractHolidayProvider($this->holidayCalculator),
            default => null,
        };
    }
}
