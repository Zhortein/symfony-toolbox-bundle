<?php

namespace Zhortein\SymfonyToolboxBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DatatableExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('datatable', [DatatableExtensionRuntime::class, 'renderDatatable'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }
}
