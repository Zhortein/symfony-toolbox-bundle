<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

readonly class CustomPagination implements PaginationInterface
{
    /**
     * @param array<int, mixed> $items
     */
    public function __construct(
        private int $totalItemCount,
        private int $currentPageNumber,
        private int $itemNumberPerPage,
        private array $items)
    {
    }

    public function getTotalItemCount(): int
    {
        return $this->totalItemCount;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    /**
     * Retrieves the number of items displayed per page.
     *
     * @return int the number of items per page
     */
    public function getItemNumberPerPage(): int
    {
        return $this->itemNumberPerPage;
    }

    /**
     * Returns the items.
     *
     * @return array<int, mixed> the list of items
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
