<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Sensiolabs\GotenbergBundle\Enumeration\PaperSize;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

class ExportPdfService extends ExportService
{
    private GotenbergPdfInterface $gotenbergPdf;

    public function setGotenbergClient(GotenbergPdfInterface $gotenbergPdf): self
    {
        $this->gotenbergPdf = $gotenbergPdf;

        return $this;
    }

    public function export(AbstractDatatable $datatable, Request $request, string $datatableName): Response
    {
        $results = $this->queryBuilder->getQuery()->getResult();

        return $this->gotenbergPdf
            ->html()
            ->content('@ZhorteinSymfonyToolbox/datatables/export-pdf.html.twig', [
                'headers' => $this->getHeaders($datatable),
                'data' => is_array($results) ? array_map(function ($result) use ($datatable) {
                    /* @phpstan-ignore-next-line */
                    return $this->extractRowData($result, $datatable);
                }, $results) : [],
            ])
            ->landscape()
            ->paperStandardSize(PaperSize::A4) // This is an enum listing the most commonly known formats.
            ->generate()
            ->stream()
        ;
    }
}
