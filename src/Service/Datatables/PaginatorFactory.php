<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Knp\Component\Pager\PaginatorInterface as KnpPaginator;

class PaginatorFactory
{
    public const string PAGINATOR_KNP = 'knp';
    public const string PAGINATOR_CUSTOM = 'custom';

    public function __construct(private readonly KnpPaginator $paginator)
    {
    }

    public function createPaginator(?string $mode = null): PaginatorInterface
    {
        return match ($mode ?? self::PAGINATOR_CUSTOM) {
            self::PAGINATOR_KNP => new KnpPaginatorAdapter($this->paginator),
            default => new CustomPaginatorAdapter(),
        };
    }
}
