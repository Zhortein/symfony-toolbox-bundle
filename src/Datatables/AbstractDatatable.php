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
     * Example:
     * [
     *  [
     *      'name' => 'id',
     *      'label' => 'Identifier',
     *      'searchable' => true,
     *      'sortable' => true,
     *      'header' => [
     *          'translate' => true,
     *          'keep_default_classes' => true,
     *          'class' => 'myCssClasses',
     *          'data' => ['custom-dataname' => 'myValue', ],
     *      ],
     *      'dataset' => [
     *          'translate' => false,
     *          'keep_default_classes' => true,
     *          'class' => 'myCssClassesForData',
     *          'data' => ['mycustom-dataname' => 'myOtherValue', ],
     *      ],
     *      'footer' => [
     *          'translate' => false,
     *          'auto' => 'count',
     *          'keep_default_classes' => true,
     *          'class' => 'myCssClassesForFooter',
     *          'data' => ['myfooter-dataname' => 'myFooterValue', ],
     *      ],
     *  ], ['name' => 'label', 'label' => 'Name', 'searchable' => true, 'sortable' => true,],].
     *
     * @var array<int, array<string, string|bool>>
     */
    protected array $columns = [];

    /**
     * Datatable options.
     * Example :
     * [
     *  'defaultPageSize' => 10,
     *  'defaultSort' => [
     *      'column' => 'id',
     *      'order' => 'asc',
     *  ],
     *  'searchable' => true,
     *  'sortable' => true,
     *  'autoColumns' => false,
     *  'options' => [
     *      'thead' => [
     *          'keep_default_classes' => true,
     *          'class' => 'myCssClasses',
     *          'data' => ['custom-dataname' => 'myValue', ],
     *      ],
     *      'tbody' => [
     *           'keep_default_classes' => true,
     *           'class' => 'myCssClasses',
     *           'data' => ['custom-dataname' => 'myValue', ],
     *       ],
     *      'tfoot' => [
     *           'keep_default_classes' => true,
     *           'class' => 'myCssClasses',
     *           'data' => ['custom-dataname' => 'myValue', ],
     *       ],
     *  ]
     * ].
     *
     * @var array<string, string|int|bool|string[]>
     */
    protected array $options = [];

    private array $globalOptions = [];
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

    public function setGlobalOptions(array $globalOptions): self
    {
        $this->globalOptions = $globalOptions;

        return $this;
    }

    public function getGlobalOptions(): array
    {
        return $this->globalOptions;
    }

    public function setCssMode(string $cssMode): self
    {
        if (!Configuration::isCssModeValid($cssMode)) {
            $this->cssMode = $this->globalOptions['css_mode'] ?? Configuration::DEFAULT_DATATABLE_CSS_MODE;
        } else {
            $this->cssMode = $cssMode;
        }

        return $this;
    }

    public function getCssMode(): string
    {
        return $this->cssMode;
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Adds a new column to the table configuration.
     *
     * @param string                $name       the name of the column
     * @param string                $label      the label of the column
     * @param bool                  $searchable Indicates if the column is searchable. Defaults to true.
     * @param bool                  $sortable   Indicates if the column is sortable. Defaults to true.
     * @param ?string               $sqlAlias   optional SQL alias for the column
     * @param array<string, string> $header     optional header configuration array
     * @param array<string, string> $dataset    optional dataset configuration array
     * @param array<string, string> $footer     optional footer configuration array
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
        string $template = '',
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
        $column = array_filter($columns, static fn ($column) => $column['nameAs'] === $asName);

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

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        if (!array_key_exists('defaultPageSize', $this->options)) {
            $this->options['defaultPageSize'] = $this->globalOptions['items_per_page'] ?? Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
        }
        if (!array_key_exists('defaultSort', $this->options)) {
            if (!empty($this->columns)) {
                $this->options['defaultSort'] = ['column' => $this->columns[0]['name'], 'order' => 'asc'];
            } else {
                $this->options['defaultSort'] = ['column' => null, 'order' => 'asc'];
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

    public function addOption(string $name, $value): self
    {
        switch ($name) {
            case 'defaultPageSize':
                if (!is_int($value)) {
                    $value = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
                }
                break;
            case 'defaultSort':
                if (!is_array($value) || empty($value)) {
                    if (!empty($this->columns)) {
                        $value = ['column' => $this->columns[0]['name'], 'order' => 'asc'];
                    } else {
                        $value = ['column' => null, 'order' => 'asc'];
                    }
                }
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
                break;
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, string|null>
     */
    public function getDefaultSort(): array
    {
        return $this->getOptions()['defaultSort'];
    }

    public function getTranslationDomain(): string
    {
        return $this->getOptions()['translationDomain'] ?? '';
    }

    public function getDefaultPageSize(): int
    {
        return (int) $this->getOptions()['defaultPageSize'];
    }

    public function isSortable(): bool
    {
        return (bool) $this->getOptions()['sortable'];
    }

    public function isSearchable(): bool
    {
        return (bool) $this->getOptions()['searchable'];
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
        if ($this->options['searchable']) {
            // The datatable must be searchable to use search features...
            $columns = $this->getColumns();

            $searchParts = [];
            foreach ($columns as $column) {
                $searchExpression = '';
                if ($column['searchable']) {
                    // Only search on "searchable" columns

                    switch ($column['datatype']) {
                        case 'integer':
                            $searchExpression .= sprintf('%s.%s = :search%s', $column['sqlAlias'], $column['name'], $searchParamCount);
                            $queryBuilder
                                ->setParameter('search'.$searchParamCount, (int) $search);
                            break;
                        case 'double':
                            $searchExpression .= sprintf('%s.%s = :search%s', $column['sqlAlias'], $column['name'], $searchParamCount);
                            $queryBuilder
                                ->setParameter('search'.$searchParamCount, (float) $search);
                            break;
                        case 'string':
                            $searchExpression .= sprintf('%s.%s LIKE :search%s', $column['sqlAlias'], $column['name'], $searchParamCount);
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

    abstract public function configure(): array;

    public function setQueryBuilder(): self
    {
        $this->queryBuilder = $this->em->createQueryBuilder()
            ->select($this->getMainAlias() ?? 't')
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
                ->addSelect(sprintf('%s.%s AS %s', $column['sqlAlias'], $column['name'], $column['nameAs']));
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
     * @param array<string, array<string, int|string>> $cachedTypes
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
