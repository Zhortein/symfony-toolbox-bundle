<?php

namespace Zhortein\SymfonyToolboxBundle\Attribute;

use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsDatatable
{
    /**
     * Constructor for configuring a datatable.
     *
     * @param string $name name of the datatable; must not be empty
     * @param array<int, array{
     *          name: string,
     *          label: string,
     *          searchable?: bool,
     *          sortable?: bool,
     *          exportable?: bool,
     *          nameAs?: string,
     *          alias?: string,
     *          sqlAlias?: string,
     *          datatype?: string,
     *          template?: string,
     *          header?: array{
     *              translate?: bool,
     *              keep_default_classes?: bool,
     *              class?: string,
     *              data?: array<string, mixed>
     *          },
     *          dataset?: array{
     *              translate?: bool,
     *              keep_default_classes?: bool,
     *              class?: string,
     *              data?: array<string, mixed>
     *          },
     *          footer?: array{
     *              translate?: bool,
     *              auto?: string,
     *              keep_default_classes?: bool,
     *              class?: string,
     *              data?: array<string, mixed>
     *          }
     *      }> $columns Configuration for each column, including properties like 'name', 'label', 'searchable', 'sortable', 'exportable', and others
     * @param int                                                    $defaultPageSize   default number of items per page; uses a predefined constant by default
     * @param array<int, array<string, string>>                      $defaultSort       default sort configuration, consisting of field and order
     * @param bool                                                   $searchable        boolean flag indicating if the table supports search functionality
     * @param bool                                                   $sortable          boolean flag indicating if the table supports sorting functionality
     * @param bool                                                   $exportable        boolean flag indicating if the table supports exports functionality
     * @param bool                                                   $autoColumns       whether columns should be automatically constructed from the request
     * @param array<string, array{label?: string, template: string}> $actionColumn      configuration for an "action" column, if any; contains 'label' and 'template'
     * @param array<string, array{label?: string, template: string}> $selectorColumn    configuration for a "selector" column, if any; contains 'label' and 'template'
     * @param string                                                 $translationDomain translation domain for the datatable; if empty, no translations are applied
     * @param array<string, mixed>                                   $options           additional configuration options for table, including 'table', 'thead', 'tfoot', and 'pagination'
     *
     * @throws \InvalidArgumentException if the $name argument is empty
     */
    public function __construct(
        public string $name,
        public array $columns = [], // Each column: ['name' => '', 'label' => '', 'searchable' => true, 'sortable' => true, 'alias' => 't', 'nameAs' => 'myBeautifulName', 'header' => ['keep_default_classes' => true, 'class' => '', 'style' => '', 'data' => ['key' => 'value', ]], 'dataset' => ['keep_default_classes' => true, 'class' => '', 'style' => '', 'data' => ['key' => 'value', ]], 'footer' => ['keep_default_classes' => true, 'auto' => '...', 'class' => '', 'style' => '', 'data' => ['key' => 'value', ]]]
        public int $defaultPageSize = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE, // ex: 10
        public array $defaultSort = [], // ex: [['field' => 'id', 'order' => 'asc'], ]
        public bool $searchable = true, // User can perform searches
        public bool $sortable = true, // User can change sorting
        public bool $exportable = true, // User can export data
        public bool $autoColumns = false, // Construct columns from request then merge column settings (settings erase auto cols detection)
        public array $actionColumn = [], // Add an "action" column ['label' => 'Actions', 'template' => '']
        public array $selectorColumn = [], // Add a "selector" column ['label' => '', 'template' => '']
        public string $translationDomain = '', // Define data translation domain for this Datatable, no translations if empty
        public array $options = [], // Additional Options ['table' => ['keep_default_classes' => true, 'class' => '', 'style' => '', 'data' => ['key' => 'value', ]], 'thead' => [], 'tfoot' => [], 'pagination' => []]
    ) {
        if (empty($name)) {
            throw new \InvalidArgumentException('You must specify a name for a Datatable.');
        }
    }

    /**
     * Converts the datatable configuration to an associative array.
     *
     * @return array<string, mixed> the datatable configuration as an associative array
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
            'exportable' => $this->exportable,
            'autoColumns' => $this->autoColumns,
            'actionColumn' => $this->actionColumn,
            'selectorColumn' => $this->selectorColumn,
            'translationDomain' => $this->translationDomain,
            'options' => $this->options,
        ];
    }
}
