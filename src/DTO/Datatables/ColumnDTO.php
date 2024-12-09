<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\Service\StringTools;

class ColumnDTO
{
    protected string $mainAlias = AbstractDatatable::DEFAULT_MAIN_ALIAS;

    public function __construct(
        public string $name,
        public string $label,
        public bool $searchable = true,
        public bool $sortable = true,
        public ?string $nameAs = '',
        public ?string $alias = '',
        public ?string $sqlAlias = '',
        public string $datatype = 'string',
        public string $template = '',
        public ColumnHeaderDTO $header = new ColumnHeaderDTO(),
        public ColumnDatasetDTO $dataset = new ColumnDatasetDTO(),
        public ColumnFooterDTO $footer = new ColumnFooterDTO(),
        public bool $autoColumns = false,
        public bool $isEnum = false,
        public bool $isTranslatableEnum = false,
    ) {
    }

    protected function completeFields(): void
    {
        if (empty($this->alias ?? '') || !StringTools::isValidSqlAlias($this->alias)) {
            $this->alias = $this->mainAlias;
        }

        if (empty($this->sqlAlias ?? '') || !StringTools::isValidSqlAlias($this->sqlAlias)) {
            $this->sqlAlias = $this->mainAlias;
        }

        if (empty($this->nameAs ?? '') || !StringTools::isValidSqlAlias($this->nameAs)) {
            if (!isset($this->sqlAlias) || $this->sqlAlias === $this->mainAlias) {
                $this->nameAs = $this->name;
            } else {
                $this->nameAs = $this->sqlAlias.'_'.$this->name;
            }
        }
    }

    /**
     * Creates an instance of the class from the provided data array.
     *
     * @param array{
     *     name: string,
     *     label: string,
     *     searchable?: bool,
     *     sortable?: bool,
     *     nameAs?: string,
     *     alias?: string,
     *     sqlAlias?: string,
     *     datatype?: string,
     *     template?: string,
     *     header?: array{
     *         translate?: bool,
     *         keep_default_classes?: bool,
     *         class?: string,
     *         data?: array<string, string|int|float|bool|null>
     *     },
     *     dataset?: array{
     *         translate?: bool,
     *         keep_default_classes?: bool,
     *         class?: string,
     *         data?: array<string, string|int|float|bool|null>
     *     },
     *     footer?: array{
     *         translate?: bool,
     *         keep_default_classes?: bool,
     *         class?: string,
     *         data?: array<string, string|int|float|bool|null>
     *     },
     *     autoColumns?: bool,
     *     isEnum?: bool,
     *     isTranslatableEnum?: bool
     * } $data
     */
    public static function fromArray(array $data, string $mainAlias = AbstractDatatable::DEFAULT_MAIN_ALIAS): self
    {
        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new \InvalidArgumentException('The "name" key is required and must be a string in ColumnDTO.');
        }
        if (!isset($data['label']) || !is_string($data['label'])) {
            throw new \InvalidArgumentException('The "label" key is required and must be a string in ColumnDTO.');
        }
        $column = new self(
            name: $data['name'],
            label: $data['label'],
            searchable: $data['searchable'] ?? true,
            sortable: $data['sortable'] ?? true,
            nameAs: $data['nameAs'] ?? $data['name'],
            alias: $data['alias'] ?? $mainAlias,
            sqlAlias: $data['sqlAlias'] ?? $mainAlias,
            datatype: $data['datatype'] ?? 'string',
            template: $data['template'] ?? '',
            header: ColumnHeaderDTO::fromArray($data['header'] ?? []),
            dataset: ColumnDatasetDTO::fromArray($data['dataset'] ?? []),
            footer: ColumnFooterDTO::fromArray($data['footer'] ?? []),
            autoColumns: $data['autoColumns'] ?? false,
            isEnum: $data['isEnum'] ?? false,
            isTranslatableEnum: $data['isTranslatableEnum'] ?? false,
        );
        $column->mainAlias = $mainAlias;
        $column->completeFields();

        return $column;
    }

    /**
     * @return array{
     *      name: string,
     *      label: string,
     *      searchable: bool,
     *      sortable: bool,
     *      nameAs?: string|null,
     *      alias?: string|null,
     *      sqlAlias?: string|null,
     *      datatype: string,
     *      template: string,
     *      header: array{
     *          translate?: bool,
     *          keep_default_classes?: bool,
     *          class?: string,
     *          data?: array<string, string|int|float|bool|null>
     *      },
     *      dataset: array{
     *          translate?: bool,
     *          keep_default_classes?: bool,
     *          class?: string,
     *          data?: array<string, string|int|float|bool|null>
     *      },
     *      footer: array{
     *          translate?: bool,
     *          keep_default_classes?: bool,
     *          class?: string,
     *          data?: array<string, string|int|float|bool|null>
     *      },
     *      autoColumns: bool,
     *      isEnum: bool,
     *      isTranslatableEnum: bool
     *  }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'nameAs' => $this->nameAs,
            'alias' => $this->alias,
            'sqlAlias' => $this->sqlAlias,
            'datatype' => $this->datatype,
            'template' => $this->template,
            'header' => $this->header->toArray(),
            'dataset' => $this->dataset->toArray(),
            'footer' => $this->footer->toArray(),
            'autoColumns' => $this->autoColumns,
            'isEnum' => $this->isEnum,
            'isTranslatableEnum' => $this->isTranslatableEnum,
        ];
    }
}
