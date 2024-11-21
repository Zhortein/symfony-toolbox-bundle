<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

class DatatableResponse
{
    public function __construct(
        private int $total,
        private int $filtered,
        private array $data,
    ) {
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'filtered' => $this->filtered,
            'data' => $this->data,
        ];
    }
}
