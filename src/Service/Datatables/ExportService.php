<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

abstract class ExportService
{
    public function __construct(protected QueryBuilder $queryBuilder)
    {
    }

    abstract public function export(AbstractDatatable $datatable, Request $request, string $datatableName): Response;

    /**
     * @param array<int|string, bool|float|int|string|null> $row
     *
     * @return array<int|string, bool|float|int|string|null>
     */
    protected function extractRowData(array $row, AbstractDatatable $datatable): array
    {
        $data = [];
        foreach ($datatable->getColumns() as $column) {
            $data[] = $row[$column['nameAs'] ?? $column['name']] ?? '';
        }

        return $data;
    }
}
