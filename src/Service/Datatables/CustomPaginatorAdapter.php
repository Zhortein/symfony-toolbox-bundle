<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CustomPaginatorAdapter implements PaginatorInterface
{
    public function paginate(mixed $target, int $page, int $limit, array $options = []): PaginationInterface
    {
        if ($target instanceof QueryBuilder) {
            $query = $target->getQuery();
            $query->setFirstResult(($page - 1) * $limit);
            $query->setMaxResults($limit);

            $paginator = new Paginator($query);
            $items = iterator_to_array($paginator->getIterator());
            $totalItemCount = count($paginator);
        } elseif (is_array($target)) {
            $totalItemCount = count($target);
            $items = array_slice($target, ($page - 1) * $limit, $limit);
        } else {
            throw new \InvalidArgumentException('Unsupported target type for pagination.');
        }

        /* @var array<int, mixed> $items */
        return new CustomPagination(
            totalItemCount: $totalItemCount,
            currentPageNumber: $page,
            itemNumberPerPage: $limit,
            items: $items
        );
    }
}
