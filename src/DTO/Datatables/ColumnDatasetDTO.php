<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class ColumnDatasetDTO extends ColumnPartDTO
{
    /**
     * @param array{
     *      translate?: bool,
     *      keep_default_classes?: bool,
     *      class?: string,
     *      data?: array<string, string|int|float|bool|null>
     *  } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            translate: $data['translate'] ?? true,
            keepDefaultClasses: $data['keep_default_classes'] ?? true,
            class: $data['class'] ?? '',
            data: DataAttributeDTO::fromArray($data['data'] ?? [])
        );
    }
}
