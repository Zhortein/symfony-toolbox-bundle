<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\Service\StringTools;

abstract class AbstractDatatable
{
    public const string DEFAULT_TRANSLATION_DOMAIN = 'zhortein_symfony_toolbox-datatable';

    protected ?QueryBuilder $queryBuilder = null;

    protected bool $displayFooter = false;

    /**
     * Columns definitions. Each column is represented by an array.
     *
     * @var array<int, array{
     *         name: string,
     *         label: string,
     *         searchable?: bool,
     *         sortable?: bool,
     *         nameAs?: string,
     *         alias?: string,
     *         sqlAlias?: string,
     *         datatype?: string,
     *         template?: string,
     *         header?: array{
     *             translate?: bool,
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         },
     *         dataset?: array{
     *             translate?: bool,
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         },
     *         footer?: array{
     *             translate?: bool,
     *             auto?: string,
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         }
     *     }>
     */
    protected array $columns = [];

    /**
     * Datatable options.
     *
     * @var array{
     *     defaultPageSize?: int,
     *     defaultSort?: array<int, array{
     *         field: string,
     *         order: 'asc'|'desc'
     *     }>,
     *     searchable?: bool,
     *     sortable?: bool,
     *     autoColumns?: bool,
     *     options?: array{
     *         thead?: array{
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         },
     *         tbody?: array{
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         },
     *         tfoot?: array{
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         }
     *     },
     *     actionColumn?: array{template: string, label: string},
     *     selectorColumn?: array{label: string, template?: string},
     *     translationDomain?: string
     * }
     */
    protected array $options = [];

    /**
     * Global datatables options.
     *
     * @var array{
     *     css_mode: string,
     *     items_per_page: int,
     *     paginator: string,
     *     ux_icons: bool,
     *     ux_icons_options: array{
     *          icon_first: string,
     *          icon_previous: string,
     *          icon_next: string,
     *          icon_last: string,
     *          icon_search: string,
     *          icon_true: string,
     *          icon_false: string,
     *          icon_sort_neutral: string,
     *          icon_sort_asc: string,
     *          icon_sort_desc: string,
     *          icon_filter: string,
     *     }
     * }
     */
    private array $globalOptions = Configuration::DEFAULT_CONFIGURATION;
    private string $mainAlias = 't';

    protected string $cssMode = Configuration::DEFAULT_DATATABLE_CSS_MODE;

    public function __construct(protected EntityManagerInterface $em)
    {
        $this->configure();
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    public function getDisplayFooter(): bool
    {
        return $this->displayFooter;
    }

    public function getMainAlias(): string
    {
        return $this->mainAlias ?? 't';
    }

    /**
     * Set the main SQL alias.
     *
     * @param string $mainAlias the main alias to set
     *
     * @return $this
     *
     * @throws \InvalidArgumentException if the provided alias is not valid
     */
    public function setMainAlias(string $mainAlias): self
    {
        if (!StringTools::isValidSqlAlias($mainAlias)) {
            throw new \InvalidArgumentException('The main alias must be a valid SQL alias.');
        }
        $this->mainAlias = $mainAlias;

        return $this;
    }

    public function getStimulusControllerName(): string
    {
        return 'zhortein--symfony-toolbox-bundle--datatable';
    }

    /**
     * Define the global options.
     *
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
     *  } $globalOptions
     *
     * @return $this
     */
    public function setGlobalOptions(array $globalOptions): self
    {
        $this->globalOptions = $globalOptions;

        return $this;
    }

    /**
     * Retrieves the global options.
     *
     * @return array{
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
     *  } An array representing the global options
     */
    public function getGlobalOptions(): array
    {
        return $this->globalOptions;
    }

    /**
     * Set the CSS mode for the configuration.
     *
     * @param string $cssMode the CSS mode to be set
     *
     * @return $this
     */
    public function setCssMode(string $cssMode): self
    {
        if (!Configuration::isCssModeValid($cssMode)) {
            if (Configuration::isCssModeValid($this->globalOptions['css_mode'])) {
                $this->cssMode = $this->globalOptions['css_mode'];
            } else {
                $this->cssMode = Configuration::DEFAULT_DATATABLE_CSS_MODE;
            }
        } else {
            $this->cssMode = $cssMode;
        }

        return $this;
    }

    public function getCssMode(): string
    {
        return $this->cssMode;
    }

    /**
     * Set the columns for the application.
     *
     * @param array<int, array{
     *         name: string,
     *         label: string,
     *         searchable?: bool,
     *         sortable?: bool,
     *         nameAs?: string,
     *         alias?: string,
     *         sqlAlias?: string,
     *         datatype?: string,
     *         template?: string,
     *         header?: array{
     *             translate?: bool,
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         },
     *         dataset?: array{
     *             translate?: bool,
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         },
     *         footer?: array{
     *             translate?: bool,
     *             auto?: string,
     *             keep_default_classes?: bool,
     *             class?: string,
     *             data?: array<string, mixed>
     *         }
     *     }> $columns
     *
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array<int, array{
     *        name: string,
     *        label: string,
     *        searchable?: bool,
     *        sortable?: bool,
     *        nameAs?: string,
     *        alias?: string,
     *        sqlAlias?: string,
     *        datatype?: string,
     *        template?: string,
     *        header?: array{
     *            translate?: bool,
     *            keep_default_classes?: bool,
     *            class?: string,
     *            data?: array<string, mixed>
     *        },
     *        dataset?: array{
     *            translate?: bool,
     *            keep_default_classes?: bool,
     *            class?: string,
     *            data?: array<string, mixed>
     *        },
     *        footer?: array{
     *            translate?: bool,
     *            auto?: string,
     *            keep_default_classes?: bool,
     *            class?: string,
     *            data?: array<string, mixed>
     *        }
     *    }>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Adds a new column to the table configuration.
     *
     * @param string                                                                                                           $name       the name of the column
     * @param string                                                                                                           $label      the label of the column
     * @param bool                                                                                                             $searchable Indicates if the column is searchable. Defaults to true.
     * @param bool                                                                                                             $sortable   Indicates if the column is sortable. Defaults to true.
     * @param ?string                                                                                                          $sqlAlias   optional SQL alias for the column
     * @param array{translate?: bool, keep_default_classes?: bool, class?: string, data?: array<string, mixed>}                $header     optional header configuration array
     * @param array{translate?: bool, keep_default_classes?: bool, class?: string, data?: array<string, mixed>}                $dataset    optional dataset configuration array
     * @param array{translate?: bool, auto?: string, keep_default_classes?: bool, class?: string, data?: array<string, mixed>} $footer     optional footer configuration array
     */
    public function addColumn(
        string $name,
        string $label,
        bool $searchable = true,
        bool $sortable = true,
        ?string $sqlAlias = null,
        ?array $header = [],
        ?array $dataset = [],
        ?array $footer = [],
        ?string $nameAs = '',
        ?string $dataType = '',
        ?string $template = '',
    ): self {
        $this->columns[] = [
            'name' => $name,
            'label' => $label,
            'searchable' => $searchable,
            'sortable' => $sortable,
            'sqlAlias' => $sqlAlias ?? $this->getMainAlias(),
            'header' => $header ?? [],
            'dataset' => $dataset ?? [],
            'footer' => $footer ?? [],
            'nameAs' => $nameAs ?? '',
            'datatype' => $dataType ?? '',
            'template' => $template ?? '',
        ];

        return $this;
    }

    /**
     * Retrieves the absolute column name from its alias.
     *
     * @param string $asName          the alias name used to find the corresponding column
     * @param bool   $withAsStatement whether to include an 'AS' statement in the returned string
     *
     * @return string the fully qualified column name optionally with an 'AS' statement
     *
     * @throws \InvalidArgumentException if the column with the given alias is not found
     */
    public function getFullyQualifiedColumnFromNameAs(string $asName, bool $withAsStatement = false): string
    {
        $columns = $this->getColumns();
        $alias = $this->getMainAlias();

        // Recherche prioritaire sur 'nameAs'
        $column = array_filter($columns, static fn ($column) => ($column['nameAs'] ?? '') === $asName);

        // Si aucune correspondance, cherche par 'name'
        if (empty($column)) {
            $column = array_filter($columns, static fn ($column) => $column['name'] === $asName);
        }

        // Si aucune colonne n'est trouvée
        if (empty($column)) {
            throw new \InvalidArgumentException('Unable to find a column with the name '.$asName);
        }

        // Utilise le premier résultat trouvé
        $column = reset($column);

        $sqlAlias = $column['sqlAlias'] ?? $alias;
        $name = $column['name'];

        // Formate la colonne
        return sprintf('%s.%s', $sqlAlias, $name).($withAsStatement ? ' AS '.$asName : '');
    }

    /**
     * Validates and initializes the table options.
     *
     * Ensures that essential keys are present in the options array
     * with default values, validating sections such as 'thead', 'tbody',
     * 'tfoot', and 'pagination'. Defaults include 'keep_default_classes'
     * set to true and 'class' as an empty string if not specified.
     */
    public function validateTableOptions(): void
    {
        if (!isset($this->options['options']) || !is_array($this->options['options'])) {
            $this->options['options'] = [];
        }

        foreach (['thead', 'tbody', 'tfoot', 'pagination'] as $key) {
            if (!isset($this->options['options'][$key])) {
                $this->options['options'][$key] = [];
            }

            if (is_array($this->options['options'][$key])) {
                if (!isset($this->options['options'][$key]['keep_default_classes'])) {
                    $this->options['options'][$key]['keep_default_classes'] = true;
                }

                if (!isset($this->options['options'][$key]['class'])) {
                    $this->options['options'][$key]['class'] = '';
                }
            }
        }
    }

    /**
     * Validates the columns of the table configuration and complete missing values with defaults.
     *
     * Ensures each column has a "name" and a "label". Sets default values for
     * "searchable" and "sortable" attributes if they are not defined.
     *
     * @throws \InvalidArgumentException if a column does not have the required "name" and "label"
     */
    public function validateColumns(): void
    {
        $this->displayFooter = false;
        $columns = $this->getColumns();

        foreach ($columns as &$column) {
            // Check that each column have at least a name and a label
            if (!isset($column['name'], $column['label'])) {
                throw new \InvalidArgumentException('Each column must have a "name" and a "label".');
            }

            // All column will be automatically aliased unless an explicit alias is given (and valid)
            if (empty($column['nameAs'] ?? '') || !StringTools::isValidSqlAlias($column['nameAs'])) {
                if (!isset($column['sqlAlias']) || $column['sqlAlias'] === $this->getMainAlias()) {
                    $column['nameAs'] = $column['name'];
                } else {
                    $column['nameAs'] = $column['sqlAlias'].'_'.$column['name'];
                }
            }

            // Default to true if not defined
            if (!isset($column['searchable'])) {
                $column['searchable'] = true;
            }

            // Default to true if not defined
            if (!isset($column['sortable'])) {
                $column['sortable'] = true;
            }

            // Default to string in case a column can't be defined
            if (empty($column['datatype'] ?? '')) {
                $column['datatype'] = 'string';
            }

            // Default to an empty template, real default will be applied after column_type detection
            if (!isset($column['template'])) {
                $column['template'] = '';
            }

            // Default to true if not defined @todo Implement autoColumns mode (Read Entity metadata and construct the columns automatically)
            if (!isset($column['autoColumns'])) {
                $column['autoColumns'] = false;
            }

            foreach (['header', 'dataset', 'footer'] as $key) {
                if (!isset($column[$key])) {
                    $column[$key] = [];
                }
                if (!isset($column[$key]['translate'])) {
                    if ('header' === $key) {
                        // By default, when a translation domain is given, only translate header labels...
                        $column[$key]['translate'] = !empty($this->getTranslationDomain());
                    } else {
                        $column[$key]['translate'] = false;
                    }
                }
                if (!isset($column[$key]['keep_default_classes'])) {
                    $column[$key]['keep_default_classes'] = true;
                }
                if (!isset($column[$key]['css'])) {
                    $column[$key]['css'] = '';
                }

                if ('footer' === $key) {
                    if (!isset($column[$key]['auto'])) {
                        $column[$key]['auto'] = '';
                    }
                    // @todo Datatable auto-footer types should be constants + handled for each type + if empty don't display the footer line
                    if (!in_array($column[$key]['auto'], ['count', 'sum', 'avg', 'min', 'max'], true)) {
                        $column[$key]['auto'] = '';
                    }

                    if (!empty($column[$key]['auto'])) {
                        $this->displayFooter = true;
                    }
                }
            }
        }

        $this->setColumns($columns);
    }

    /**
     * Set the configuration options.
     *
     * @param array{
     *      defaultPageSize?: int,
     *      defaultSort?: array<int, array{
     *          field: string,
     *          order: 'asc'|'desc'
     *      }>,
     *      searchable?: bool,
     *      sortable?: bool,
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
     *  } $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array{
     *      defaultPageSize?: int,
     *      defaultSort?: array<int, array{
     *          field: string,
     *          order: 'asc'|'desc'
     *      }>,
     *      searchable?: bool,
     *      sortable?: bool,
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
     *      }
     *  }
     */
    public function getOptions(): array
    {
        if (!array_key_exists('defaultPageSize', $this->options)) {
            $this->options['defaultPageSize'] = $this->globalOptions['items_per_page'] ?? Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
        }
        if (!array_key_exists('defaultSort', $this->options)) {
            if (!empty($this->columns)) {
                $this->options['defaultSort'] = [['field' => $this->columns[0]['name'], 'order' => 'asc']];
            } else {
                $this->options['defaultSort'] = [['field' => '', 'order' => 'asc']];
            }
        }
        if (!array_key_exists('searchable', $this->options)) {
            $this->options['searchable'] = true;
        }
        if (!array_key_exists('sortable', $this->options)) {
            $this->options['sortable'] = true;
        }

        return $this->options;
    }

    /**
     * Adds an option to the current configuration.
     *
     * @param string $name  The name of the option to add.
     *                      Possible values are 'defaultPageSize', 'defaultSort', 'translationDomain', 'searchable', 'sortable', or 'autoColumns'.
     * @param mixed  $value The value of the option. The expected type varies based on the option name:
     *                      - 'defaultPageSize': int
     *                      - 'defaultSort': array<int, array{
     *                      field: string,
     *                      order: 'asc'|'desc'
     *                      }>
     *                      - 'translationDomain': string
     *                      - 'searchable', 'sortable', 'autoColumns': bool
     *
     * @return self returns the current instance for method chaining
     *
     * @throws \InvalidArgumentException if validation checks fail for 'defaultSort'
     */
    public function addOption(string $name, mixed $value): self
    {
        switch ($name) {
            case 'actionColumn': // an array with 'template' and 'label'
                if (!is_array($value) || !isset($value['template'], $value['label']) || !is_string($value['template']) || !is_string($value['label'])) {
                    throw new \InvalidArgumentException('The "actionColumn" option must be an array with "template" and "label" as strings.');
                }
                break;
            case 'selectorColumn': // an array with 'template' and 'label'
                if (!is_array($value) || !isset($value['label']) || !is_string($value['label'])) {
                    throw new \InvalidArgumentException('The "selectorColumn" option must be an array with "label" as a string.');
                }
                // @todo Enable this test when selectorColumn template will be implemented
                /*if (isset($value['template']) && !is_string($value['template'])) {
                    throw new \InvalidArgumentException('The "template" in "selectorColumn" must be a string.');
                }*/
                break;
            case 'options': // an  array<string,string|int|float|bool|null>
                if (!is_array($value)) {
                    throw new \InvalidArgumentException('The "options" option must be an array.');
                }
                break;
            case 'defaultPageSize':
                if (!is_int($value)) {
                    $value = (int) Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
                }
                break;
            case 'defaultSort':
                if (!is_array($value) || empty($value)) {
                    if (!empty($this->columns)) {
                        $value = [['field' => $this->columns[0]['name'], 'order' => 'asc']];
                    } else {
                        $value = [['field' => null, 'order' => 'asc']];
                    }
                }

                // Vérifie que chaque élément de $value a la structure attendue
                foreach ($value as $item) {
                    if (!is_array($item)) {
                        throw new \InvalidArgumentException('Each item in the multiSort array must be an array.');
                    }

                    if (!isset($item['field'], $item['order'])) {
                        throw new \InvalidArgumentException('Each item must contain a "field" and a "order" key.');
                    }

                    if (!is_string($item['field'])) {
                        throw new \InvalidArgumentException('The "field" value must be a string.');
                    }

                    if (!in_array($item['order'], ['asc', 'desc'], true)) {
                        throw new \InvalidArgumentException('The "order" value must be either "asc" or "desc".');
                    }
                }
                $value = (array) $value;
                break;
            case 'translationDomain':
                if (!is_string($value) || empty($value)) {
                    $value = '';
                }
                break;
            case 'searchable':
            case 'sortable':
            case 'autoColumns':
                $value = (bool) $value;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The option "%s" is not supported.', $name));
        }

        /* @phpstan-ignore-next-line */
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @return array<int, array{field: string, order: 'asc'|'desc'}>
     */
    public function getDefaultSort(): array
    {
        return $this->getOptions()['defaultSort'] ?? [['field' => '', 'order' => 'asc']];
    }

    public function getTranslationDomain(): string
    {
        return $this->getOptions()['translationDomain'] ?? '';
    }

    public function getDefaultPageSize(): int
    {
        return (int) ($this->getOptions()['defaultPageSize'] ?? Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE);
    }

    public function isSortable(): bool
    {
        return (bool) ($this->getOptions()['sortable'] ?? true);
    }

    public function isSearchable(): bool
    {
        return (bool) ($this->getOptions()['searchable'] ?? true);
    }

    /**
     * Applies search criteria to the QueryBuilder based on the provided search string.
     * This method incorporates search functionality only if the datatable is marked as searchable
     * and considers only columns that are flagged as searchable.
     *
     * @param string $search the search string to apply to the query
     */
    public function applySearch(QueryBuilder $queryBuilder, string $search): void
    {
        $searchParamCount = 0;
        if ($this->isSearchable()) {
            // The datatable must be searchable to use search features...
            $columns = $this->getColumns();

            $searchParts = [];
            foreach ($columns as $column) {
                $searchExpression = '';
                if ($column['searchable'] ?? true) {
                    // Only search on "searchable" columns

                    switch ($column['datatype'] ?? '') {
                        case 'integer':
                            $searchExpression .= sprintf('%s.%s = :search%s', $column['sqlAlias'] ?? $this->getMainAlias(), $column['name'], $searchParamCount);
                            $queryBuilder
                                ->setParameter('search'.$searchParamCount, (int) $search);
                            break;
                        case 'double':
                            $searchExpression .= sprintf('%s.%s = :search%s', $column['sqlAlias'] ?? $this->getMainAlias(), $column['name'], $searchParamCount);
                            $queryBuilder
                                ->setParameter('search'.$searchParamCount, (float) $search);
                            break;
                        case 'string':
                            $searchExpression .= sprintf('%s.%s LIKE :search%s', $column['sqlAlias'] ?? $this->getMainAlias(), $column['name'], $searchParamCount);
                            $queryBuilder
                                ->setParameter('search'.$searchParamCount, "%$search%");
                            break;
                        default:
                            break;
                    }

                    if (!empty($searchExpression)) {
                        $searchParts[] = $searchExpression;
                    }
                    ++$searchParamCount;
                }
            }

            if (count($searchParts) > 0) {
                $queryBuilder->andWhere('('.implode(' OR ', $searchParts).')');
            }
        }
        $this->applyStaticFilters($queryBuilder);
    }

    /**
     * Redefine this method to set static filters on the QueryBuilder.
     * All searches, sorts, ... on the Datatable will use those static filters.
     */
    public function applyStaticFilters(QueryBuilder $queryBuilder): void
    {
    }

    abstract public function configure(): self;

    public function setQueryBuilder(): self
    {
        $this->queryBuilder = $this->em->createQueryBuilder()
            ->select($this->getMainAlias())
            ->from($this->getEntityClass(), $this->mainAlias)
        ;

        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        $this->setQueryBuilder();
        $this->validateColumns();

        // Compose columns selected query. Each column is named with its alias to allow dynamic filtering / sorting...
        foreach ($this->columns as $column) {
            $this->queryBuilder
                ?->addSelect(sprintf('%s.%s AS %s', $column['sqlAlias'] ?? $this->getMainAlias(), $column['name'], $column['nameAs'] ?? $column['name']));
        }

        if (null === $this->queryBuilder) {
            throw new \RuntimeException('The query builder is not set.');
        }

        return $this->queryBuilder;
    }

    /**
     * Gets the fully qualified class name of the main entity.
     *
     * This abstract method should be implemented by subclasses to return the
     * class name of the specific main entity they are related to.
     *
     * @return class-string The fully qualified class name of the entity
     */
    abstract public function getEntityClass(): string;

    /**
     * Calculates a unique checksum for a given datatable.
     *
     * @param string $datatableName the name of the datatable
     *
     * @return string the calculated checksum
     *
     * @throws \RuntimeException if unable to generate a checksum due to a JSON encoding error
     */
    public function calculateChecksum(string $datatableName): string
    {
        $metadata = $this->em->getClassMetadata($this->getQueryBuilder()->getRootEntities()[0]);

        // Combine des éléments pour générer un hash unique
        $data = [
            'datatableName' => $datatableName,
            'columns' => $metadata->getFieldNames(),
            'associations' => $metadata->getAssociationNames(),
        ];

        try {
            // Crée un checksum unique
            return md5(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (\JsonException $e) {
            throw new \RuntimeException('Unable to generate a checksum for the datatable.', 0, $e);
        }
    }

    /**
     * @param array<string, array{
     *       rank: int,
     *       datatype: string,
     *       isEnum: bool,
     *       isTranslatableEnum: bool,
     *  }> $cachedTypes
     */
    public function setCachedTypes(array $cachedTypes): void
    {
        foreach ($cachedTypes as $columnTypeDefinition) {
            if (is_int($columnTypeDefinition['rank'])) {
                $this->columns[$columnTypeDefinition['rank']]['datatype'] = (string) $columnTypeDefinition['datatype'];
                $this->columns[$columnTypeDefinition['rank']]['isEnum'] = (bool) $columnTypeDefinition['isEnum'];
                $this->columns[$columnTypeDefinition['rank']]['isTranslatableEnum'] = $columnTypeDefinition['isEnum'] && $columnTypeDefinition['isTranslatableEnum'];

                if (empty($this->columns[$columnTypeDefinition['rank']]['template'])) {
                    $this->columns[$columnTypeDefinition['rank']]['template'] = match ((string) $columnTypeDefinition['datatype']) {
                        'enum' => '@ZhorteinSymfonyToolbox/datatables/column_types/_enum.html.twig',
                        'enum_translatable' => '@ZhorteinSymfonyToolbox/datatables/column_types/_enum-translatable.html.twig',
                        'array' => '@ZhorteinSymfonyToolbox/datatables/column_types/_array.html.twig',
                        'boolean' => '@ZhorteinSymfonyToolbox/datatables/column_types/_boolean.html.twig',
                        \DateTimeInterface::class, \DateTime::class, \DateTimeImmutable::class => '@ZhorteinSymfonyToolbox/datatables/column_types/_datetime.html.twig',
                        \DateInterval::class => '@ZhorteinSymfonyToolbox/datatables/column_types/_dateinterval.html.twig',
                        \DatePeriod::class => '@ZhorteinSymfonyToolbox/datatables/column_types/_dateperiod.html.twig',
                        \DateTimeZone::class => '@ZhorteinSymfonyToolbox/datatables/column_types/_timezone.html.twig',
                        'double' => '@ZhorteinSymfonyToolbox/datatables/column_types/_double.html.twig',
                        'integer' => '@ZhorteinSymfonyToolbox/datatables/column_types/_integer.html.twig',
                        'object' => '@ZhorteinSymfonyToolbox/datatables/column_types/_object.html.twig',
                        'resource' => '@ZhorteinSymfonyToolbox/datatables/column_types/_resource.html.twig',
                        'resource (closed)' => '@ZhorteinSymfonyToolbox/datatables/column_types/_resource-closed.html.twig',
                        'unknown type' => '@ZhorteinSymfonyToolbox/datatables/column_types/_unknown.html.twig',
                        'NULL' => '@ZhorteinSymfonyToolbox/datatables/column_types/_null.html.twig',
                        default => '@ZhorteinSymfonyToolbox/datatables/column_types/_string.html.twig',
                    };
                }
            }
        }
    }

    public function isIconUxMode(): bool
    {
        return $this->getGlobalOptions()['ux_icons'] ?? true;
    }

    public function getIcon(string $iconName): string
    {
        $default = $this->isIconUxMode() ? 'carbon:unknown' : '';

        return $this->getGlobalOptions()['ux_icons_options'][$iconName] ?? $default;
    }
}
