<?php

namespace Zhortein\SymfonyToolboxBundle\Attribute;

use Zhortein\SymfonyToolboxBundle\Service\StringTools;

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
        $this->name = StringTools::sanitizeFileName($name);
    }
}
