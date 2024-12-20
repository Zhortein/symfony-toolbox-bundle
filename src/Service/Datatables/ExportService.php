<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

abstract class ExportService
{
    public function __construct(
        protected QueryBuilder $queryBuilder,
        protected TranslatorInterface $translator,
    ) {
    }

    abstract public function export(AbstractDatatable $datatable, Request $request): Response;

    protected function getFilename(string $datatableName, string $extension): string
    {
        return sprintf('%s_export_%s.%s', $datatableName, date('Y-m-d_H-i-s'), $extension);
    }

    /**
     * @return string[]
     */
    protected function getHeaders(AbstractDatatable $datatable): array
    {
        if (!empty($datatable->getTranslationDomain())) {
            $headers = [];
            foreach ($datatable->getColumns() as $column) {
                $headers[] = $this->translator->trans($column->label, [], $datatable->getTranslationDomain());
            }

            return $headers;
        }

        return array_column($datatable->getColumns(), 'label');
    }

    /**
     * @param array<int|string, bool|float|int|string|null> $row
     *
     * @return array<int|string, bool|float|int|string|null>
     */
    protected function extractRowData(array $row, AbstractDatatable $datatable): array
    {
        $data = [];
        foreach ($datatable->getColumns() as $column) {
            $data[] = $row[$column->nameAs ?? $column->name] ?? '';
        }

        return $data;
    }
}
