<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

final class ExportOptionsDTO
{
    public function __construct(
        public bool $enabledByDefault = true,
        public bool $exportCsv = true,
        public bool $exportPdf = false,
        public bool $exportExcel = true,
    ) {
    }

    /**
     * @param array<string, bool> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            enabledByDefault: $data['enabled_by_default'] ?? true,
            exportCsv: $data['export_csv'] ?? true,
            exportPdf: $data['export_pdf'] ?? false,
            exportExcel: $data['export_excel'] ?? true,
        );
    }
}
