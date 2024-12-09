<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class SortOptionDTO
{
    public const string SORT_ASC = 'asc';
    public const string SORT_DESC = 'desc';

    public function __construct(
        public string $field = '',
        public string $order = 'asc',
    ) {
    }

    /**
     * @return array{
     *      field: string,
     *      order: string
     *  }
     */
    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'order' => $this->order,
        ];
    }

    /**
     * @param array{
     *     field?: string,
     *     order?: string
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            field: isset($data['field']) && is_string($data['field']) ? $data['field'] : '',
            order: isset($data['order']) && in_array($data['order'], [self::SORT_ASC, self::SORT_DESC], true) ? $data['order'] : self::SORT_ASC
        );
    }
}
