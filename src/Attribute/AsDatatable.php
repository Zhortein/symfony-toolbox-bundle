<?php

namespace Zhortein\SymfonyToolboxBundle\Attribute;

use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsDatatable
{
    /**
     * Constructor for the Datatable.
     *
     * @param string $name            the name of the datatable
     * @param array  $columns         configuration for each column
     * @param int    $defaultPageSize default number of items per page
     * @param array  $defaultSort     default sorting configuration
     * @param bool   $searchable      indicates if the search functionality is enabled
     * @param bool   $sortable        indicates if the sorting functionality is enabled
     * @param array  $options         additional options for the datatable
     *
     * @throws \InvalidArgumentException if name is empty
     */
    public function __construct(
        public string $name,
        public array $columns = [], // Each column: ['name' => '', 'label' => '', 'searchable' => true, 'sortable' => true, 'alias' => 't', 'header' => ['keep_default_classes' => true, 'class' => '', 'style' => '', 'data' => ['key' => 'value', ]], 'dataset' => ['keep_default_classes' => true, 'class' => '', 'style' => '', 'data' => ['key' => 'value', ]], 'footer' => ['keep_default_classes' => true, 'auto' => '...', 'class' => '', 'style' => '', 'data' => ['key' => 'value', ]]]
        public int $defaultPageSize = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE, // ex: 10
        public array $defaultSort = [], // ex: ['column' => 'id', 'order' => 'asc']
        public bool $searchable = true, // User can perform searches
        public bool $sortable = true, // User can change sorting
        public bool $autoColumns = false, // Construct columns from request then merge column settings (settings erase auto cols detection)
        public string $translationDomain = '', // Define data translation domain for this Datatable, no translations if empty
        public array $options = [], // Additional Options
    ) {
        if (empty($name)) {
            throw new \InvalidArgumentException('You must specify a name for a Datatable.');
        }
    }

    /**
     * Converts the datatable configuration to an associative array.
     *
     * @return array the datatable configuration as an associative array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'columns' => $this->columns,
            'defaultPageSize' => $this->defaultPageSize,
            'defaultSort' => $this->defaultSort,
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'autoColumns' => $this->autoColumns,
            'translationDomain' => $this->translationDomain,
            'options' => $this->options,
        ];
    }
}
