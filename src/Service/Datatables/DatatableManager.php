<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

readonly class DatatableManager
{
    /**
     * @param array<string, AbstractDatatable> $datatables
     */
    public function __construct(private array $datatables)
    {
    }

    public function getDatatable(string $name): ?AbstractDatatable
    {
        return $this->datatables[$name] ?? null;
    }
}
