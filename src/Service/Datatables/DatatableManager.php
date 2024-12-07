<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\Service\Cache\CacheManager;

readonly class DatatableManager
{
    /**
     * @param array<string, AbstractDatatable> $datatables
     * @param array<string, array{
     *      defaultPageSize?: int,
     *      defaultSort?: array<int, array{
     *          field: string,
     *          order: 'asc'|'desc'
     *      }>,
     *      searchable?: bool,
     *      sortable?: bool,
     *      exportable?: bool,
     *      autoColumns?: bool,
     *      options?: array{
     *          thead?: array{
     *              keep_default_classes?: bool,
     *              class?: string,
     *              data?: array<string, mixed>
     *          },
     *          tbody?: array{
     *              keep_default_classes?: bool,
     *              class?: string,
     *              data?: array<string, mixed>
     *          },
     *          tfoot?: array{
     *              keep_default_classes?: bool,
     *              class?: string,
     *              data?: array<string, mixed>
     *          }
     *      },
     *      actionColumn?: array{template: string, label: string},
     *      selectorColumn?: array{label: string, template?: string},
     *      translationDomain?: string
     *  }> $datatableOptions
     * @param array{
     *      css_mode: string,
     *      items_per_page: int,
     *      paginator: string,
     *      ux_icons: bool,
     *      ux_icons_options: array{
     *           icon_first: string,
     *           icon_previous: string,
     *           icon_next: string,
     *           icon_last: string,
     *           icon_search: string,
     *           icon_true: string,
     *           icon_false: string,
     *           icon_sort_neutral: string,
     *           icon_sort_asc: string,
     *           icon_sort_desc: string,
     *           icon_filter: string,
     *      }
     *  }   $globalOptions
     */
    public function __construct(
        private array $datatables,
        private array $datatableOptions,
        private array $globalOptions,
        private CacheManager $cache,
    ) {
    }

    /**
     * Retrieves a global option by its key.
     *
     * @param string $key     the key for the global option to retrieve
     * @param mixed  $default the default value to return if the key does not exist
     *
     * @return mixed the value of the global option associated with the key, or the default value if the key does not exist
     */
    public function getGlobalOption(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->globalOptions) ? $this->globalOptions[$key] : $default;
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
        $cssMode = $this->getGlobalOption('css_mode', Configuration::DEFAULT_DATATABLE_CSS_MODE);
        if (!is_string($cssMode)) {
            return Configuration::DEFAULT_DATATABLE_CSS_MODE;
        }

        return $cssMode;
    }

    /**
     * Retrieves the options for a specific datatable by its name.
     *
     * @param string $name the name of the datatable
     *
     * @return array<string, mixed>|null the options of the datatable, or null if not found
     */
    public function getDatatableOptions(string $name): ?array
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
            $options = $this->getDatatableOptions($name);
            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    if ('columns' === $key && is_array($value)) {
                        /**
                         * @var array{
                         *          name: string,
                         *          label: string,
                         *          searchable?: bool,
                         *          sortable?: bool,
                         *          nameAs?: string,
                         *          alias?: string,
                         *          sqlAlias?: string,
                         *          datatype?: string,
                         *          template?: string,
                         *          header?: array{
                         *              translate?: bool,
                         *              keep_default_classes?: bool,
                         *              class?: string,
                         *              data?: array<string, mixed>
                         *          },
                         *          dataset?: array{
                         *              translate?: bool,
                         *              keep_default_classes?: bool,
                         *              class?: string,
                         *              data?: array<string, mixed>
                         *          },
                         *          footer?: array{
                         *              translate?: bool,
                         *              auto?: string,
                         *              keep_default_classes?: bool,
                         *              class?: string,
                         *              data?: array<string, mixed>
                         *          }
                         *      } $column
                         */
                        foreach ($value as $column) {
                            $datatable->addColumn(
                                $column['name'],
                                $column['label'],
                                $column['searchable'] ?? true,
                                $column['sortable'] ?? true,
                                $column['alias'] ?? null,
                                $column['header'] ?? ['keep_default_classes' => true, 'class' => ''],
                                $column['dataset'] ?? ['keep_default_classes' => true, 'class' => ''],
                                $column['footer'] ?? ['keep_default_classes' => true, 'class' => '', 'auto' => ''],
                                $column['nameAs'] ?? '',
                                $column['datatype'] ?? '',
                                $column['template'] ?? ''
                            );
                        }
                    }

                    if (in_array($key, ['actionColumn', 'selectorColumn', 'defaultPageSize', 'defaultSort', 'searchable', 'sortable', 'exportable', 'options', 'autoColumns', 'translationDomain'])) {
                        $datatable->addOption($key, $value);
                    }
                }
            }

            $datatable->validateColumns();
            $datatable->validateTableOptions();

            // Get cached column_types
            /** @var array<string, array{
             * rank: int,
             * datatype: string,
             * isEnum: bool,
             * isTranslatableEnum: bool,
             * }> $cachedTypes */
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
     * @return array<string, array{
     *      rank: int,
     *      datatype: string,
     *      isEnum: bool,
     *      isTranslatableEnum: bool,
     * }> An associative array where keys are column names and values are arrays containing 'rank', 'datatype', 'isEnum', and 'isTranslatableEnum'
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
                $detectedType = gettype($value);
                $isEnum = false;
                $isTranslatableEnum = false;
                if ('object' === $detectedType && is_object($value)) {
                    $detectedType = get_class($value);
                    $isEnum = $value instanceof \BackedEnum;
                    $isTranslatableEnum = method_exists($value, 'label') && method_exists(
                        $value,
                        'getTranslationDomain'
                    );
                }

                foreach ($datatable->getColumns() as $rank => $column) {
                    if (($column['nameAs'] ?? '') === $key) {
                        $types[$key] = [
                            'rank' => $rank,
                            'datatype' => $detectedType,  // Type détecté
                            'isEnum' => $isEnum,
                            'isTranslatableEnum' => $isTranslatableEnum,
                        ];
                        break;
                    }
                }
            }
        }

        return $types;
    }
}
