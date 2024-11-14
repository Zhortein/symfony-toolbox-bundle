<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

class HolidayProviderManager
{
    /**
     * @var array<string, AbstractHolidayProvider>
     */
    private array $providers;

    /**
     * @param array<string, AbstractHolidayProvider> $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function getProvider(string $countryCode): ?AbstractHolidayProvider
    {
        return $this->providers[strtoupper($countryCode)] ?? null;
    }
}
