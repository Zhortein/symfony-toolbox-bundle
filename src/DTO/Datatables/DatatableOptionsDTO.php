<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;

class DatatableOptionsDTO
{
    public function __construct(
        public string $name = 'default',
        public int $defaultPageSize = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE,
        /** @var array<int, SortOptionDTO> */
        public array $defaultSort = [new SortOptionDTO()],
        public bool $searchable = true,
        public bool $sortable = true,
        public bool $exportable = true,
        public bool $exportCsv = true,
        public bool $exportPdf = false,
        public bool $exportExcel = true,
        public bool $autoColumns = false,
        public TableOptionsDTO $options = new TableOptionsDTO(),
        public ?ActionColumnDTO $actionColumn = null,
        public ?SelectorColumnDTO $selectorColumn = null,
        public string $translationDomain = 'messages',
    ) {
    }

    /**
     * @param array{
     *     name: string,
     *     defaultPageSize?: int,
     *     defaultSort?: array<int, array{
     *          field: string,
     *          order: string
     *     }>,
     *     searchable?: bool,
     *     sortable?: bool,
     *     exportable?: bool,
     *     exportCsv?: bool,
     *     exportPdf?: bool,
     *     exportExcel?: bool,
     *     autoColumns?: bool,
     *     translationDomain?: string,
     *     actionColumn?: array{
     *         label?: string,
     *         template?: string
     *     },
     *     selectorColumn?: array{
     *         label?: string,
     *         template?: string
     *     },
     *     options?: array{
     *      thead?: array{
     *        translate?: bool,
     *        keep_default_classes?: bool,
     *        class?: string,
     *        data?: array<string, string|int|float|bool|null>,
     *    },
     *      tbody?: array{
     *        translate?: bool,
     *        keep_default_classes?: bool,
     *        class?: string,
     *        data?: array<string, string|int|float|bool|null>,
     *    },
     *      tfoot?: array{
     *        translate?: bool,
     *        keep_default_classes?: bool,
     *        class?: string,
     *        data?: array<string, string|int|float|bool|null>,
     *    },
     *  }
     * } $data
     */
    public static function fromArray(array $data, GlobalOptionsDTO $globalOptions): self
    {
        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new \InvalidArgumentException('The "name" key is required and must be a string in DatatableOptionsDTO.');
        }

        $pageSize = (int) ($data['defaultPageSize'] ?? $globalOptions->itemsPerPage);
        if (empty($pageSize) || $pageSize < 1) {
            $pageSize = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
        }

        $exportEnabled = $data['exportable'] ?? $globalOptions->export->enabledByDefault;

        return new self(
            name: $data['name'],
            defaultPageSize: $pageSize,
            defaultSort: array_map(static fn ($sort) => SortOptionDTO::fromArray($sort), $data['defaultSort'] ?? []),
            searchable: $data['searchable'] ?? true,
            sortable: $data['sortable'] ?? true,
            exportable: $exportEnabled,
            exportCsv: $exportEnabled && ($data['exportCsv'] ?? $globalOptions->export->exportCsv),
            exportPdf: $exportEnabled && ($data['exportPdf'] ?? $globalOptions->export->exportPdf),
            exportExcel: $exportEnabled && ($data['exportExcel'] ?? $globalOptions->export->exportExcel),
            autoColumns: $data['autoColumns'] ?? false,
            options: TableOptionsDTO::fromArray($data['options'] ?? []),
            actionColumn: isset($data['actionColumn']) && is_array($data['actionColumn']) ? ActionColumnDTO::fromArray($data['actionColumn']) : null,
            selectorColumn: isset($data['selectorColumn']) && is_array($data['selectorColumn']) ? SelectorColumnDTO::fromArray($data['selectorColumn']) : null,
            translationDomain: $data['translationDomain'] ?? 'messages'
        );
    }
}
