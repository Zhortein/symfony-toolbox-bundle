<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

readonly class DatatableManager
{
    /**
     * @param array<string, AbstractDatatable> $datatables
     */
    public function __construct(
        private array $datatables,
        private array $globalOptions,
    )
    {}

    public function getGlobalOption(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->globalOptions) ? $this->globalOptions[$key] : $default;
    }

    public function getDatatable(string $name): ?AbstractDatatable
    {
        return $this->datatables[$name] ?? null;
    }
}
