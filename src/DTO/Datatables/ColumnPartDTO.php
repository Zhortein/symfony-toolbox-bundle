<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class ColumnPartDTO
{
    public function __construct(
        public readonly bool $translate = true,
        public readonly bool $keepDefaultClasses = true,
        public readonly string $class = '',
        public readonly DataAttributeDTO $data = new DataAttributeDTO(),
    ) {
    }

    /**
     * @return array{
     *      translate?: bool,
     *      keep_default_classes?: bool,
     *      class?: string,
     *      data?: array<string, string|int|float|bool|null>
     *  }
     */
    public function toArray(): array
    {
        return [
            'translate' => $this->translate,
            'keep_default_classes' => $this->keepDefaultClasses,
            'class' => $this->class,
            'data' => $this->data->toArray(),
        ];
    }

    /**
     * @param array{
     *     translate?: bool,
     *     keep_default_classes?: bool,
     *     class?: string,
     *     data?: array<string, string|int|float|bool|null>
     * } $data
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
