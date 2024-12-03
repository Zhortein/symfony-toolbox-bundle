<?php

namespace Zhortein\SymfonyToolboxBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsHolidayProvider
{
    /**
     * @param string[] $countryCodes Array of country codes (uppercase)
     */
    public function __construct(public array $countryCodes)
    {
        if (empty($countryCodes)) {
            throw new \InvalidArgumentException('You must specify at least one country code for a Holiday Provider.');
        }
    }
}
