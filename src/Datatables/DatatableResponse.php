<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

readonly class DatatableResponse
{
    /**
     * @param array<int, mixed> $data
     * @param array{
     *      current: int,
     *      hasPrevious: bool,
     *      nbPages: int,
     *      previous: int,
     *      pages: int[],
     *      pageSize: int,
     *      hasNext: bool,
     *      next: int,
     * } $pagination
     */
    public function __construct(
        private int $total,
        private int $filtered,
        private array $data,
        private array $pagination,
    ) {
    }

    /**
     * @return array{
     *     total: int,
     *     filtered: int,
     *     data: array<int, mixed>,
     *     pagination: array{
     *       current: int,
     *       hasPrevious: bool,
     *       nbPages: int,
     *       previous: int,
     *       pages: int[],
     *       pageSize: int,
     *       hasNext: bool,
     *       next: int,
     *  }
     * }
     */
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
