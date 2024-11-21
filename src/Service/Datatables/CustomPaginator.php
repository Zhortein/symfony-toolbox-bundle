<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Doctrine\ORM\Tools\Pagination\Paginator;

class CustomPaginator implements PaginatorInterface
{
    public function paginate(object $queryBuilder, int $page, int $limit): array
    {
        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return iterator_to_array(new Paginator($query, true));
    }
}
