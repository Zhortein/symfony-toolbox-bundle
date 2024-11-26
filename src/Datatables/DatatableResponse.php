<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

readonly class DatatableResponse
{
    public function __construct(
        private int $total,
        private int $filtered,
        private array $data,
        private array $pagination,
    ) {
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'filtered' => $this->filtered,
            'data' => $this->data,
            'pagination' => $this->pagination,
        ];
    }
}
