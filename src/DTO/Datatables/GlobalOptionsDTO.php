<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;

final class GlobalOptionsDTO
{
    public function __construct(
        public string $cssMode = Configuration::DEFAULT_DATATABLE_CSS_MODE,
        public int $itemsPerPage = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE,
        public string $paginator = Configuration::DEFAULT_DATATABLE_PAGINATOR,
        public ExportOptionsDTO $export = new ExportOptionsDTO(),
        public bool $uxIcons = true,
        public IconsOptionsDTO $uxIconsOptions = new IconsOptionsDTO(),
    ) {
    }

    /**
     * @param array{
     *       css_mode: string,
     *       items_per_page: int,
     *       paginator: string,
     *       export: array{
     *            enabled_by_default: bool,
     *            export_csv: bool,
     *            export_pdf: bool,
     *            export_excel: bool,
     *       },
     *       ux_icons: bool,
     *       ux_icons_options: array{
     *            icon_first: string,
     *            icon_previous: string,
     *            icon_next: string,
     *            icon_last: string,
     *            icon_search: string,
     *            icon_true: string,
     *            icon_false: string,
     *            icon_sort_neutral: string,
     *            icon_sort_asc: string,
     *            icon_sort_desc: string,
     *            icon_filter: string,
     *            icon_export_csv: string,
     *            icon_export_pdf: string,
     *            icon_export_excel: string,
     *       }
     *   } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            cssMode: $data['css_mode'] ?? Configuration::DEFAULT_DATATABLE_CSS_MODE,
            itemsPerPage: $data['items_per_page'] ?? Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE,
            paginator: $data['paginator'] ?? Configuration::DEFAULT_DATATABLE_PAGINATOR,
            export: ExportOptionsDTO::fromArray($data['export'] ?? []),
            uxIcons: $data['ux_icons'] ?? true,
            uxIconsOptions: IconsOptionsDTO::fromArray($data['ux_icons_options'] ?? [])
        );
    }
}
