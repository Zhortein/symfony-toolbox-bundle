<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorFactory;

class DatatableFactory
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PaginatorFactory $paginatorFactory,
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    public function createDatatableService(): DatatableService
    {
        return new DatatableService($this->twig, $this->paginatorFactory, $this->parameterBag);
    }
}