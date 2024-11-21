<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Knp\Component\Pager\PaginatorInterface as KnpPaginator;

class KnpPaginatorAdapter implements PaginatorInterface
{
    public function __construct(private readonly KnpPaginator $paginator)
    {
    }

    public function paginate(object $queryBuilder, int $page, int $limit): array
    {
        return $this->paginator->paginate($queryBuilder, $page, $limit)->getItems();
    }
}
