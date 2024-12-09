<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

final class IconsOptionsDTO
{
    public function __construct(
        public string $iconFirst = 'bi:chevron-double-left',
        public string $iconPrevious = 'bi:chevron-left',
        public string $iconNext = 'bi:chevron-right',
        public string $iconLast = 'bi:chevron-double-right',
        public string $iconSearch = 'bi:search',
        public string $iconTrue = 'bi:check',
        public string $iconFalse = 'bi:x',
        public string $iconSortNeutral = 'mdi:sort',
        public string $iconSortAsc = 'bi:sort-alpha-down',
        public string $iconSortDesc = 'bi:sort-alpha-up',
        public string $iconFilter = 'mi:filter',
        public string $iconExportCsv = 'bi:filetype-csv',
        public string $iconExportPdf = 'bi:filetype-pdf',
        public string $iconExportExcel = 'bi:filetype-xlsx',
    ) {
    }

    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            iconFirst: $data['icon_first'] ?? 'bi:chevron-double-left',
            iconPrevious: $data['icon_previous'] ?? 'bi:chevron-left',
            iconNext: $data['icon_next'] ?? 'bi:chevron-right',
            iconLast: $data['icon_last'] ?? 'bi:chevron-double-right',
            iconSearch: $data['icon_search'] ?? 'bi:search',
            iconTrue: $data['icon_true'] ?? 'bi:check',
            iconFalse: $data['icon_false'] ?? 'bi:x',
            iconSortNeutral: $data['icon_sort_neutral'] ?? 'mdi:sort',
            iconSortAsc: $data['icon_sort_asc'] ?? 'bi:sort-alpha-down',
            iconSortDesc: $data['icon_sort_desc'] ?? 'bi:sort-alpha-up',
            iconFilter: $data['icon_filter'] ?? 'mi:filter',
            iconExportCsv: $data['icon_export_csv'] ?? 'bi:filetype-csv',
            iconExportPdf: $data['icon_export_pdf'] ?? 'bi:filetype-pdf',
            iconExportExcel: $data['icon_export_excel'] ?? 'bi:filetype-xlsx'
        );
    }

    public function getIcon(string $iconName, bool $uxIconMode = true): string
    {
        return match ($iconName) {
            'icon_first' => $this->iconFirst,
            'icon_previous' => $this->iconPrevious,
            'icon_next' => $this->iconNext,
            'icon_last' => $this->iconLast,
            'icon_search' => $this->iconSearch,
            'icon_true' => $this->iconTrue,
            'icon_false' => $this->iconFalse,
            'icon_sort_neutral' => $this->iconSortNeutral,
            'icon_sort_asc' => $this->iconSortAsc,
            'icon_sort_desc' => $this->iconSortDesc,
            'icon_filter' => $this->iconFilter,
            'icon_export_csv' => $this->iconExportCsv,
            'icon_export_pdf' => $this->iconExportPdf,
            'icon_export_excel' => $this->iconExportExcel,
            default => $uxIconMode ? 'carbon:unknown' : '',
        };
    }
}
