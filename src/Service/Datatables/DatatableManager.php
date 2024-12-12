<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnCachedTypeDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\DatatableOptionsDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\GlobalOptionsDTO;
use Zhortein\SymfonyToolboxBundle\Service\Cache\CacheManager;

class DatatableManager
{
    /**
     * @var array<string, ColumnDTO[]>
     */
    private array $datatableColumns = [];

    /**
     * @var array<string, DatatableOptionsDTO>
     */
    private array $datatableOptions = [];

    private GlobalOptionsDTO $globalOptions;

    /**
     * @param array<string, AbstractDatatable> $datatables
     * @param array<string, array<int, array{
     *       name: string,
     *       label: string,
     *       searchable: bool,
     *       sortable: bool,
     *       nameAs?: string,
     *       alias?: string,
     *       sqlAlias?: string,
     *       datatype: string,
     *       template: string,
     *       header: array{
     *           translate?: bool,
     *           keep_default_classes?: bool,
     *           class?: string,
     *           data?: array<string, string|int|float|bool|null>
     *       },
     *       dataset: array{
     *           translate?: bool,
     *           keep_default_classes?: bool,
     *           class?: string,
     *           data?: array<string, string|int|float|bool|null>
     *       },
     *       footer: array{
     *           translate?: bool,
     *           keep_default_classes?: bool,
     *           class?: string,
     *           data?: array<string, string|int|float|bool|null>
     *       },
     *       autoColumns: bool,
     *       isEnum: bool,
     *       isTranslatableEnum: bool,
     *       enumClass: string,
     *   }>>         $rawDatatableColumns
     * @param array<string, array{
     *       name: string,
     *       defaultPageSize: int,
     *       defaultSort: array<int, array{
     *            field: string,
     *            order: string
     *       }>,
     *       searchable: bool,
     *       sortable: bool,
     *       exportable: bool,
     *       exportCsv: bool,
     *       exportPdf: bool,
     *       exportExcel: bool,
     *       autoColumns: bool,
     *       translationDomain: string,
     *       actionColumn?: array{
     *           label?: string,
     *           template?: string
     *       },
     *       selectorColumn?: array{
     *           label?: string,
     *           template?: string
     *       },
     *       options: array{
     *        thead?: array{
     *          translate?: bool,
     *          keep_default_classes?: bool,
     *          class?: string,
     *          data?: array<string, string|int|float|bool|null>,
     *      },
     *        tbody?: array{
     *          translate?: bool,
     *          keep_default_classes?: bool,
     *          class?: string,
     *          data?: array<string, string|int|float|bool|null>,
     *      },
     *        tfoot?: array{
     *          translate?: bool,
     *          keep_default_classes?: bool,
     *          class?: string,
     *          data?: array<string, string|int|float|bool|null>,
     *      },
     *    }
     *   }> $rawDatatableOptions
     * @param array{
     *         css_mode: string,
     *         items_per_page: int,
     *         paginator: string,
     *         export: array{
     *              enabled_by_default: bool,
     *              export_csv: bool,
     *              export_pdf: bool,
     *              export_excel: bool,
     *         },
     *         ux_icons: bool,
     *         ux_icons_options: array{
     *              icon_first: string,
     *              icon_previous: string,
     *              icon_next: string,
     *              icon_last: string,
     *              icon_search: string,
     *              icon_true: string,
     *              icon_false: string,
     *              icon_sort_neutral: string,
     *              icon_sort_asc: string,
     *              icon_sort_desc: string,
     *              icon_filter: string,
     *              icon_export_csv: string,
     *              icon_export_pdf: string,
     *              icon_export_excel: string,
     *         }
     *     } $rawGlobalOptions
     */
    public function __construct(
        private readonly array $datatables,
        private readonly array $rawDatatableColumns,
        private readonly array $rawDatatableOptions,
        private readonly array $rawGlobalOptions,
        private readonly CacheManager $cache,
    ) {
        $this->globalOptions = GlobalOptionsDTO::fromArray($this->rawGlobalOptions);
        foreach ($this->rawDatatableColumns as $name => $rawDatatableColumn) {
            $this->datatableColumns[$name] = [];
            foreach ($rawDatatableColumn as $column) {
                $this->datatableColumns[$name][] = ColumnDTO::fromArray($column);
            }
        }
        foreach ($this->rawDatatableOptions as $name => $rawDatatableOption) {
            $this->datatableOptions[$name] = DatatableOptionsDTO::fromArray($rawDatatableOption, $this->globalOptions);
        }
    }

    public function getGlobalOptions(): GlobalOptionsDTO
    {
        return $this->globalOptions;
    }

    /**
     * Retrieves the CSS mode setting.
     *
     * This method fetches the CSS mode from the global options. If the fetched value is not a string, it defaults
     * to the predefined constant value for the CSS mode.
     *
     * @return string The CSS mode string. Default is returned if the global option is not set.
     */
    public function getCssMode(): string
    {
        return $this->globalOptions->cssMode ?? Configuration::DEFAULT_DATATABLE_CSS_MODE;
    }

    /**
     * Retrieves the options for a specific datatable by its name.
     *
     * @param string $name the name of the datatable
     */
    public function getDatatableOptions(string $name): ?DatatableOptionsDTO
    {
        return $this->datatableOptions[$name] ?? null;
    }

    /**
     * Retrieves the datatable object by name and configures it with global options and specific options.
     *
     * @param string $name the name of the datatable to retrieve
     *
     * @return AbstractDatatable|null the configured datatable object or null if it doesn't exist
     *
     * @throws \RuntimeException if unable to build datatable types
     */
    public function getDatatable(string $name): ?AbstractDatatable
    {
        $datatable = $this->datatables[$name] ?? null;
        if ($datatable instanceof AbstractDatatable) {
            $checksum = $datatable->calculateChecksum($name);
            $datatable->setGlobalOptions($this->globalOptions);
            $datatable->setCssMode($this->getCssMode());
            if (isset($this->datatableColumns[$name])) {
                $datatable->setColumns($this->datatableColumns[$name]);
            } else {
                throw new \RuntimeException(sprintf('Unable to find datatable columns for datatable "%s".', $name));
            }
            if (isset($this->datatableOptions[$name])) {
                $datatable->setOptions($this->datatableOptions[$name]);
            } else {
                $datatable->setOptions(new DatatableOptionsDTO());
            }
            $datatable->validateColumns();

            // Get cached column_types
            /** @var ColumnCachedTypeDTO[] $cachedTypes */
            $cachedTypes = $this->cache->get($checksum, function () use ($datatable) {
                return $this->buildDatatableTypesForCache($datatable);
            });

            // Load column_types in datatable columns
            if (is_array($cachedTypes)) {
                $datatable->setCachedTypes($cachedTypes);
            } else {
                throw new \RuntimeException('Unable to build datatable types.');
            }
        }

        return $datatable;
    }

    /**
     * Analyzes the results of a datatable query to determine and cache the PHP types of each column.
     *
     * This function executes the query associated with the provided datatable,
     * limited to a single result, to inspect data types and metadata of each column.
     * The PHP type detection includes checks for standard types, objects, and whether the objects
     * are enums or translatable enums.
     *
     * @param AbstractDatatable $datatable The datatable instance to analyze for type caching
     *
     * @return ColumnCachedTypeDTO[]
     */
    private function buildDatatableTypesForCache(AbstractDatatable $datatable): array
    {
        // Lancer la requête avec LIMIT 1
        $query = $datatable->getQueryBuilder()
            ->setMaxResults(1)
            ->getQuery();
        $result = $query->getArrayResult();

        $types = [];
        if (!empty($result[0]) && is_array($result[0])) {
            foreach ($result[0] as $key => $value) {
                // Détection du type PHP
                $detectedType = $this->detectType($value);

                foreach ($datatable->getColumns() as $rank => $column) {
                    if (($column->nameAs ?? '') === $key) {
                        $types[$key] = new ColumnCachedTypeDTO(
                            rank: $rank,
                            datatype: $detectedType['type'],
                            isEnum: $detectedType['isEnum'],
                            isTranslatableEnum: $detectedType['isTranslatableEnum'],
                        );
                        break;
                    }
                }
            }
        }

        return $types;
    }

    /**
     * @return array{
     *     type: string,
     *     isEnum: bool,
     *     isTranslatableEnum: bool
     * }
     */
    private function detectType(mixed $value): array
    {
        $detectedType = gettype($value);
        $isEnum = false;
        $enumClass = '';
        $isTranslatableEnum = false;

        if (is_object($value)) {
            $detectedType = get_class($value);

            if ($value instanceof \BackedEnum) {
                $detectedType = 'enum';
                $isEnum = true;
                $enumClass = $detectedType;

                if (method_exists($value, 'label') && method_exists($value, 'getTranslationDomain')) {
                    $isTranslatableEnum = true;
                    $detectedType = 'enum_translatable';
                }
            }

            if ($value instanceof \DateTimeInterface) {
                $detectedType = 'date';
            }
        }

        // Map des types Doctrine → types utilisateurs
        $doctrineToUserTypeMap = [
            'integer' => 'int',
            'smallint' => 'int',
            'bigint' => 'int',
            'float' => 'float',
            'decimal' => 'float',
            'boolean' => 'bool',
            'datetime' => 'datetime',
            'datetimetz' => 'datetime',
            'date' => 'date',
            'time' => 'time',
            'json' => 'json',
            'array' => 'array',
            'string' => 'string',
            'text' => 'string',
        ];

        if (isset($doctrineToUserTypeMap[$detectedType])) {
            $detectedType = $doctrineToUserTypeMap[$detectedType];
        }

        return [
            'type' => $detectedType,
            'isEnum' => $isEnum,
            'isTranslatableEnum' => $isTranslatableEnum,
            'enumClass' => $isEnum ? $enumClass : null,
        ];
    }
}
