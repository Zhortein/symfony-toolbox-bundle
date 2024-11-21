<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PaginatorFactory
{
    public const string PAGINATOR_KNP = 'knp';
    public const string PAGINATOR_CUSTOM = 'custom';

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function createPaginator(?string $mode = null): PaginatorInterface
    {
        return match ($mode ?? self::PAGINATOR_CUSTOM) {
            self::PAGINATOR_KNP => $this->container->get(KnpPaginatorAdapter::class),
            default => new CustomPaginator(),
        };
    }
}
