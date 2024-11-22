<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

interface PaginatorInterface
{
    public function paginate(object $queryBuilder, int $page, int $limit): array;
    public function getTotal(object $queryBuilder): int;
}
