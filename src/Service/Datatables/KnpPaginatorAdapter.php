<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Knp\Component\Pager\PaginatorInterface as KnpPaginator;

readonly class KnpPaginatorAdapter implements PaginatorInterface
{
    public function __construct(private KnpPaginator $paginator)
    {
    }

    public function paginate(object $queryBuilder, int $page, int $limit): array
    {
        return $this->paginator->paginate($queryBuilder, $page, $limit)->getItems();
    }

    public function getTotal(object $queryBuilder): int
    {
        return $this->paginator->paginate($queryBuilder, 1, 1)->getTotalItemCount();
    }
}
