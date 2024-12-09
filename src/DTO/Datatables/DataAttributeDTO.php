<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class DataAttributeDTO
{
    /**
     * @param array<string, string|int|float|bool|null> $attributes
     */
    public function __construct(
        public array $attributes = [],
    ) {
    }

    /**
     * @return array<string, string|int|float|bool|null>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, string|int|float|bool|null> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(attributes: $data);
    }
}
