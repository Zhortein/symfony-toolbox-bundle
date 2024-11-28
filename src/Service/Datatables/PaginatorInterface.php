<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Doctrine\ORM\QueryBuilder;

interface PaginatorInterface
{
    /**
     * Paginates the given target (QueryBuilder or array).
     *
     * @param QueryBuilder|array<int, mixed> $target  The target data to paginate
     * @param int                            $page    The current page
     * @param int                            $limit   The number of items per page
     * @param array<string, mixed>           $options Optional settings for the paginator
     *
     * @return PaginationInterface
     */
    public function paginate(mixed $target, int $page, int $limit, array $options = []): PaginationInterface;
}
