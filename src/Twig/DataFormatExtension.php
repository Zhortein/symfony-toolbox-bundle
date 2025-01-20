<?php

namespace Zhortein\SymfonyToolboxBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DataFormatExtension extends AbstractExtension
{
    /**
     * Define twig filters extended.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_duration_from_iso', [DataFormatRuntime::class, 'formatDurationIso']),
            new TwigFilter('format_duration_from_seconds', [DataFormatRuntime::class, 'formatDurationSeconds']),
        ];
    }
}