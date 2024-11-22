<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

readonly class DatatableManager
{
    /**
     * @param array<string, AbstractDatatable> $datatables
     * @param array<string, array>             $datatableOptions
     */
    public function __construct(
        private array $datatables,
        private array $datatableOptions,
        private array $globalOptions,
    ) {
    }

    public function getGlobalOption(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->globalOptions) ? $this->globalOptions[$key] : $default;
    }

    public function getDatatableOptions(string $name): ?array
    {
        return $this->datatableOptions[$name] ?? null;
    }

    public function getDatatable(string $name): ?AbstractDatatable
    {
        $datatable = $this->datatables[$name] ?? null;
        if ($datatable instanceof AbstractDatatable) {
            $options = $this->getDatatableOptions($name);
            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    if ('columns' === $key) {
                        foreach ($value as $column) {
                            $datatable->addColumn($column['name'], $column['type'], $column['searchable'] ?? true, $column['sortable'] ?? true);
                        }
                    }

                    if (in_array($key, ['defaultPageSize', 'defaultSort', 'searchable', 'sortable', 'options'])) {
                        $datatable->addOption($key, $value);
                    }
                }
            }
        }

        return $datatable;
    }
}
