<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

interface PaginationInterface
{
    public function getTotalItemCount(): int;

    public function getCurrentPageNumber(): int;

    public function getItemNumberPerPage(): int;

    /**
     * @return array<int, mixed>
     */
    public function getItems(): array;
}