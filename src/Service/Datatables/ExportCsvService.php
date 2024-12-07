<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

class ExportCsvService extends ExportService
{
    public function export(AbstractDatatable $datatable, Request $request, string $datatableName): Response
    {
        $queryBuilder = $this->queryBuilder;
        $separator = ';' === $request->query->get('separator', ';') ? ';' : ',';

        $filename = sprintf('%s_export_%s.csv', $datatableName, date('Y-m-d_H-i-s'));

        $response = new StreamedResponse(function () use ($datatable, $queryBuilder, $separator) {
            $handle = fopen('php://output', 'wb');
            if (false === $handle) {
                throw new \RuntimeException('Could not open output stream.');
            }
            $columns = array_column($datatable->getColumns(), 'label');
            fputcsv($handle, $columns, $separator);

            $results = $queryBuilder->getQuery()->getResult();
            if (is_array($results)) {
                /** @var array<int|string, bool|float|int|string|null> $row */
                foreach ($results as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    fputcsv($handle, $this->extractRowData($row, $datatable), $separator);
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }
}
