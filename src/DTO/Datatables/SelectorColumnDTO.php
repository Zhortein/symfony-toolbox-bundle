<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class SelectorColumnDTO
{
    public function __construct(
        public string $template = '',
        public string $label = '',
    ) {
    }

    /**
     * @param array{
     *     template?: string,
     *     label?: string
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            template: isset($data['template']) && is_string($data['template']) ? $data['template'] : '',
            label: isset($data['label']) && is_string($data['label']) ? $data['label'] : ''
        );
    }

    /**
     * @return array{
     *       template: string,
     *       label: string
     *   }
     */
    public function toArray(): array
    {
        return [
            'template' => $this->template,
            'label' => $this->label,
        ];
    }
}
