<?php

namespace Zhortein\SymfonyToolboxBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsDatatable
{
    public function __construct(
        public string $name,
        public array $columns = [], // Each column: ['name' => '', 'label' => '', 'searchable' => true, 'orderable' => true]
        public int $defaultPageSize = 10,
        public array $defaultSort = [], // ex: ['column' => 'id', 'order' => 'asc']
        public bool $searchable = true,
        public array $options = [], // Additional Options
    ) {
        if (empty($name)) {
            throw new \InvalidArgumentException('You must specify a name for a Datatable.');
        }
    }
}
