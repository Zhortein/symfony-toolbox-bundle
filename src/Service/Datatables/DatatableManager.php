<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;

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
            $datatable->setGlobalOptions($this->globalOptions);
            $datatable->setCssMode($this->getGlobalOption('css_mode', Configuration::DEFAULT_DATATABLE_CSS_MODE));
            $options = $this->getDatatableOptions($name);
            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    if ('columns' === $key) {
                        foreach ($value as $column) {
                            $datatable->addColumn(
                                $column['name'],
                                $column['label'],
                                $column['searchable'] ?? true,
                                $column['sortable'] ?? true,
                                $column['alias'] ?? null,
                                $column['header'] ?? ['keep_default_classes' => true, 'class' => ''],
                                $column['dataset'] ?? ['keep_default_classes' => true, 'class' => ''],
                                $column['footer'] ?? ['keep_default_classes' => true, 'class' => '', 'auto' => ''],
                            );
                        }
                    }

                    if (in_array($key, ['defaultPageSize', 'defaultSort', 'searchable', 'sortable', 'options', 'autoColumns', 'translationDomain'])) {
                        $datatable->addOption($key, $value);
                    }
                }
            }

            $datatable->validateColumns();
            $datatable->validateTableOptions();
        }

        return $datatable;
    }
}
