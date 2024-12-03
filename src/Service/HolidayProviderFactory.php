<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\BelgiumHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\EnglandHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\FranceHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\GermanyHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\HolidayCalculator;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\SpainHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\UnitedStatesHolidayProvider;

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
            'FR' => new FranceHolidayProvider($this->holidayCalculator),
            'BE' => new BelgiumHolidayProvider($this->holidayCalculator),
            'EN','GB','UK' => new EnglandHolidayProvider($this->holidayCalculator),
            'US' => new UnitedStatesHolidayProvider($this->holidayCalculator),
            'DE' => new GermanyHolidayProvider($this->holidayCalculator),
            'ES' => new SpainHolidayProvider($this->holidayCalculator),
            default => null,
        };
    }
}
