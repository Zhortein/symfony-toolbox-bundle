<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

class ExportExcelService extends ExportService
{
    public function export(AbstractDatatable $datatable, Request $request, string $datatableName): Response
    {
        // TODO: Implement export() method.
        throw new \RuntimeException('Not implemented Yet');
    }
}
