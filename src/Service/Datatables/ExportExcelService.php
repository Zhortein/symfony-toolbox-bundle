<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

class ExportExcelService extends ExportService
{
    public function export(AbstractDatatable $datatable, Request $request, string $datatableName): Response
    {
        $filename = sprintf('%s_export_%s.xlsx', $datatableName, date('Y-m-d_H-i-s'));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write header row
        $columns = array_column($datatable->getColumns(), 'label');
        $sheet->fromArray($columns, null, 'A1');

        // Write data rows
        $data = $this->queryBuilder->getQuery()->getResult();
        $rowIndex = 2; // Start from row 2 to leave room for headers
        if (is_array($data)) {
            foreach ($data as $row) {
                if (!is_array($row)) {
                    continue;
                }
                /* @phpstan-ignore-next-line */
                $sheet->fromArray($this->extractRowData($row, $datatable), null, 'A'.$rowIndex);
                ++$rowIndex;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile); // Remove temporary file

        return new Response(
            false !== $content ? $content : '',
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        );
    }
}
