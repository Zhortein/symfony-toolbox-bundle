<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Knp\Component\Pager\PaginatorInterface as KnpPaginator;

readonly class KnpPaginatorAdapter implements PaginatorInterface
{
    public function __construct(private KnpPaginator $paginator)
    {
    }

    public function paginate(mixed $target, int $page, int $limit, array $options = []): PaginationInterface
    {
        $pagination = $this->paginator->paginate($target, $page, $limit, $options);
        $items = is_array($pagination->getItems())
            ? $pagination->getItems()
            : iterator_to_array($pagination->getItems());

        return new CustomPagination(
            totalItemCount: $pagination->getTotalItemCount(),
            currentPageNumber: $pagination->getCurrentPageNumber(),
            itemNumberPerPage: $pagination->getItemNumberPerPage(),
            items: $items
        );
    }
}
