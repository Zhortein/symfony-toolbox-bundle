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
     * @param array{
     *      enabled_by_default?: bool,
     *      export_csv?: bool,
     *      export_pdf?: bool,
     *      export_excel?: bool,
     *  } $data
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

    /**
     * @return array{
     *     enabled_by_default: bool,
     *     export_csv: bool,
     *     export_pdf: bool,
     *     export_excel: bool,
     * }
     */
    public function toArray(): array
    {
        return [
            'enabled_by_default' => $this->enabledByDefault,
            'export_csv' => $this->exportCsv,
            'export_pdf' => $this->exportPdf,
            'export_excel' => $this->exportExcel,
        ];
    }
}
