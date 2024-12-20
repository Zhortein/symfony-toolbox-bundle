<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class ColumnCachedTypeDTO
{
    /**
     * @param class-string<\BackedEnum>|null $enumClassName
     */
    public function __construct(
        public int $rank,
        public string $datatype = '',
        public bool $isEnum = false,
        public bool $isTranslatableEnum = false,
        public ?string $enumClassName = null,
    ) {
    }
}
