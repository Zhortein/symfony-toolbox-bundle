<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PaginatorFactory
{
    public const string PAGINATOR_KNP = 'knp';
    public const string PAGINATOR_CUSTOM = 'custom';

    public function __construct(
        private readonly ContainerInterface $container,
        private string $mode = self::PAGINATOR_CUSTOM,
    ) {
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function createPaginator(?string $mode = null): PaginatorInterface
    {
        if (in_array($mode, [self::PAGINATOR_KNP, self::PAGINATOR_CUSTOM], true)) {
            $this->mode = $mode;
        }

        $paginator = match ($this->mode) {
            self::PAGINATOR_KNP => $this->container->get(KnpPaginatorAdapter::class),
            self::PAGINATOR_CUSTOM => $this->container->get(CustomPaginator::class),
            default => null,
        } ?? new CustomPaginator();

        if (!$paginator instanceof PaginatorInterface) {
            $paginator = new CustomPaginator();
        }

        return $paginator;
    }
}
