<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class ColumnCachedTypeDTO
{
    public function __construct(
        public int $rank,
        public string $datatype = '',
        public bool $isEnum = false,
        public bool $isTranslatableEnum = false,
        public ?string $enumClassName = null,
    ) {
    }
}
