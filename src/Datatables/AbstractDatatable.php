<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnCachedTypeDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\DatatableOptionsDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\GlobalOptionsDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\SortOptionDTO;
use Zhortein\SymfonyToolboxBundle\Service\StringTools;

abstract class AbstractDatatable
{
    public const string DEFAULT_MAIN_ALIAS = 't';
    public const string DEFAULT_TRANSLATION_DOMAIN = 'zhortein_symfony_toolbox-datatable';

    protected ?QueryBuilder $queryBuilder = null;

    protected bool $displayFooter = false;

    /**
     * @var ColumnDTO[]
     */
    protected array $columns = [];
    protected DatatableOptionsDTO $options;
    private GlobalOptionsDTO $globalOptions;
    private string $mainAlias = self::DEFAULT_MAIN_ALIAS;

    protected string $cssMode = Configuration::DEFAULT_DATATABLE_CSS_MODE;

    public function __construct(protected EntityManagerInterface $em)
    {
        $this->configure();
        $this->options = new DatatableOptionsDTO();
        $this->globalOptions = new GlobalOptionsDTO();
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

    public function setGlobalOptions(GlobalOptionsDTO $globalOptions): self
    {
        $this->globalOptions = $globalOptions;

        return $this;
    }

    public function getGlobalOptions(): GlobalOptionsDTO
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
            if (Configuration::isCssModeValid($this->globalOptions->cssMode)) {
                $this->cssMode = $this->globalOptions->cssMode;
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
     * @param ColumnDTO[] $columns
     *
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return ColumnDTO[]
     */
    public function getColumns(): array
    {
        return $this->columns;
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
        $column = array_filter($columns, static fn ($column) => ($column->nameAs ?? '') === $asName);

        // Si aucune correspondance, cherche par 'name'
        if (empty($column)) {
            $column = array_filter($columns, static fn ($column) => $column->name === $asName);
        }

        // Si aucune colonne n'est trouvée
        if (empty($column)) {
            throw new \InvalidArgumentException('Unable to find a column with the name '.$asName);
        }

        // Utilise le premier résultat trouvé
        $column = reset($column);

        $sqlAlias = $column->sqlAlias ?? $alias;
        $name = $column->name;

        // Formate la colonne
        return sprintf('%s.%s', $sqlAlias, $name).($withAsStatement ? ' AS '.$asName : '');
    }

    public function validateColumns(): void
    {
        $this->displayFooter = false;
        foreach ($this->columns as $column) {
            if (!empty($column->footer->auto)) {
                $this->displayFooter = true;
            }
        }
    }

    public function setOptions(DatatableOptionsDTO $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): DatatableOptionsDTO
    {
        if ($this->options->defaultPageSize <= 1) {
            $this->options->defaultPageSize = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
        }

        if (empty($this->options->defaultSort)) {
            if (!empty($this->columns)) {
                $this->options->defaultSort = [new SortOptionDTO(field: $this->columns[0]->name, order: 'asc')];
            } else {
                $this->options->defaultSort = [new SortOptionDTO(field: '', order: 'asc')];
            }
        }

        return $this->options;
    }

    /**
     * @return array<int, array{
     *      field: string,
     *      order: string
     *  }>
     */
    public function getDefaultSort(): array
    {
        if (empty($this->options->defaultSort)) {
            $this->options->defaultSort = [new SortOptionDTO(field: '', order: 'asc')];
        }

        return array_map(static function ($sort) {
            return $sort->toArray();
        }, $this->options->defaultSort);
    }

    public function getTranslationDomain(): string
    {
        return $this->options->translationDomain ?? '';
    }

    public function getDefaultPageSize(): int
    {
        return $this->options->defaultPageSize ?? Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
    }

    public function isExportable(): bool
    {
        return $this->options->exportable;
    }

    public function isCsvExportable(): bool
    {
        return $this->isExportable() && $this->options->exportCsv;
    }

    public function isPdfExportable(): bool
    {
        return $this->isExportable() && $this->options->exportPdf;
    }

    public function isExcelExportable(): bool
    {
        return $this->isExportable() && $this->options->exportExcel;
    }

    public function isSortable(): bool
    {
        return $this->options->sortable;
    }

    public function isSearchable(): bool
    {
        return $this->options->searchable;
    }

    public function hasSelectorColumn(): bool
    {
        return null !== $this->options->selectorColumn;
    }

    public function hasActionColumn(): bool
    {
        return null !== $this->options->actionColumn && !empty($this->options->actionColumn->template);
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
                if ($column->searchable ?? true) {
                    // Only search on "searchable" columns

                    switch ($column->datatype ?? '') {
                        case 'integer':
                            $searchExpression .= sprintf('%s.%s = :search%s', $column->sqlAlias ?? $this->getMainAlias(), $column->name, $searchParamCount);
                            $queryBuilder
                                ->setParameter('search'.$searchParamCount, (int) $search);
                            break;
                        case 'double':
                            $searchExpression .= sprintf('%s.%s = :search%s', $column->sqlAlias ?? $this->getMainAlias(), $column->name, $searchParamCount);
                            $queryBuilder
                                ->setParameter('search'.$searchParamCount, (float) $search);
                            break;
                        case 'string':
                            $searchExpression .= sprintf('%s.%s LIKE :search%s', $column->sqlAlias ?? $this->getMainAlias(), $column->name, $searchParamCount);
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
    }

    public function applyFilters(QueryBuilder $qb, array $filters): void
    {
        foreach ($filters as $index => $filter) {
            $paramName1 = 'filter_value1_'.$index;
            $paramName2 = 'filter_value2_'.$index;
            $paramNameValues = 'filter_values_'.$index; // Pour le IN / NOT IN
            $column = $filter['column'];
            $type = $filter['type'];
            $value1 = $filter['value1'];
            $value2 = $filter['value2'] ?? null;
            $values = $filter['values'] ?? [];

            switch ($type) {
                case 'equal':
                    $qb->andWhere("$column = :$paramName1")
                        ->setParameter($paramName1, $value1);
                    break;

                case 'not_equal':
                    $qb->andWhere("$column != :$paramName1")
                        ->setParameter($paramName1, $value1);
                    break;

                case 'contains':
                    $qb->andWhere("$column LIKE :$paramName1")
                        ->setParameter($paramName1, '%'.$value1.'%');
                    break;

                case 'not_contains':
                    $qb->andWhere("$column NOT LIKE :$paramName1")
                        ->setParameter($paramName1, '%'.$value1.'%');
                    break;

                case 'starts_with':
                    $qb->andWhere("$column LIKE :$paramName1")
                        ->setParameter($paramName1, $value1.'%');
                    break;

                case 'not_starts_with':
                    $qb->andWhere("$column NOT LIKE :$paramName1")
                        ->setParameter($paramName1, $value1.'%');
                    break;

                case 'ends_with':
                    $qb->andWhere("$column LIKE :$paramName1")
                        ->setParameter($paramName1, '%'.$value1);
                    break;

                case 'not_ends_with':
                    $qb->andWhere("$column NOT LIKE :$paramName1")
                        ->setParameter($paramName1, '%'.$value1);
                    break;

                case 'between':
                    $qb->andWhere("$column BETWEEN :$paramName1 AND :$paramName2")
                        ->setParameter($paramName1, $value1)
                        ->setParameter($paramName2, $value2);
                    break;

                case 'not_between':
                    $qb->andWhere("$column NOT BETWEEN :$paramName1 AND :$paramName2")
                        ->setParameter($paramName1, $value1)
                        ->setParameter($paramName2, $value2);
                    break;

                case 'in':
                    if (!empty($values)) {
                        $qb->andWhere("$column IN (:$paramName1)")
                            ->setParameter($paramName1, explode(',', $values));
                    }
                    break;

                case 'not_in':
                    if (!empty($values)) {
                        $qb->andWhere("$column NOT IN (:$paramName1)")
                            ->setParameter($paramName1, explode(',', $values));
                    }
                    break;

                case 'is_null':
                    $qb->andWhere("$column IS NULL");
                    break;

                case 'is_not_null':
                    $qb->andWhere("$column IS NOT NULL");
                    break;

                case 'is_true':
                    $qb->andWhere("$column = :$paramName1")
                        ->setParameter($paramName1, true);
                    break;

                case 'is_false':
                    $qb->andWhere("$column = :$paramName1")
                        ->setParameter($paramName1, false);
                    break;

                case 'before':
                case 'less_than':
                    $qb->andWhere("$column < :$paramName1")
                        ->setParameter($paramName1, $value1);
                    break;

                case 'less_or_equal_than':
                    $qb->andWhere("$column <= :$paramName1")
                        ->setParameter($paramName1, $value1);
                    break;

                case 'after':
                case 'greater_than':
                    $qb->andWhere("$column > :$paramName1")
                        ->setParameter($paramName1, $value1);
                    break;

                case 'greater_or_equal_than':
                    $qb->andWhere("$column >= :$paramName1")
                        ->setParameter($paramName1, $value1);
                    break;

                default:
                    break;
            }
        }
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
                ?->addSelect(sprintf('%s.%s AS %s', $column->sqlAlias ?? $this->getMainAlias(), $column->name, $column->nameAs ?? $column->name));
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
     * @param ColumnCachedTypeDTO[] $cachedTypes
     */
    public function setCachedTypes(array $cachedTypes): void
    {
        foreach ($cachedTypes as $columnTypeDefinition) {
            $rank = $columnTypeDefinition->rank;
            if (is_int($rank)) {
                $this->columns[$rank]->datatype = $columnTypeDefinition->datatype;
                $this->columns[$rank]->isEnum = $columnTypeDefinition->isEnum;
                $this->columns[$rank]->isTranslatableEnum = $columnTypeDefinition->isEnum && $columnTypeDefinition->isTranslatableEnum;
                $this->columns[$rank]->enumClass = $columnTypeDefinition->isEnum ? $columnTypeDefinition->enumClassName : '';

                $this->updateColumnTemplate($rank, $columnTypeDefinition->datatype);
            }
        }
    }

    private function getDefaultTemplateForDatatype(string $dataType): ?string
    {
        return match ($dataType) {
            'enum' => '_enum.html.twig',
            'enum_translatable' => '_enum-translatable.html.twig',
            'array' => '_array.html.twig',
            'json' => '_json.html.twig',
            'bool', 'boolean' => '_boolean.html.twig',
            \DateTimeInterface::class, \DateTime::class, \DateTimeImmutable::class, 'datetime', 'datetimetz' => '_datetime.html.twig',
            \DateInterval::class => '_dateinterval.html.twig',
            \DatePeriod::class => '_dateperiod.html.twig',
            \DateTimeZone::class => '_timezone.html.twig',
            'date' => '_date.html.twig',
            'time' => '_time.html.twig',
            'double', 'float' => '_double.html.twig',
            'int', 'integer' => '_integer.html.twig',
            'object' => '_object.html.twig',
            'resource' => '_resource.html.twig',
            'resource (closed)' => '_resource-closed.html.twig',
            'unknown type' => '_unknown.html.twig',
            'NULL' => '_null.html.twig',
            default => null,
        };
    }

    public function getDatatypeForFilters(string $dataType): string
    {
        return match ($dataType) {
            'enum', 'enum_translatable' => 'enum',
            'array', 'json', \DateInterval::class, \DatePeriod::class, 'resource', 'resource (closed)', 'unknown type', 'NULL', 'object' => '',
            'bool', 'boolean' => 'boolean',
            \DateTimeInterface::class, \DateTime::class, \DateTimeImmutable::class, 'datetime', 'datetimetz', 'date' => 'date',
            'double', 'float', 'int', 'integer' => 'number',
            default => 'string',
        };
    }

    private function updateColumnTemplate(int $rank, string $type): void
    {
        $templatePrefix = '@ZhorteinSymfonyToolbox/datatables/column_types/';
        $template = $this->getDefaultTemplateForDatatype($type) ?? '_string.html.twig';

        if (empty($this->columns[$rank]->template)) {
            $this->columns[$rank]->template = $templatePrefix.$template;
        }
    }

    public function isIconUxMode(): bool
    {
        return $this->globalOptions->uxIcons ?? true;
    }

    public function getIcon(string $iconName): string
    {
        return $this->globalOptions->uxIconsOptions->getIcon($iconName, $this->isIconUxMode());
    }

    public function getDatatableName(): string
    {
        return $this->options->name;
    }
}
