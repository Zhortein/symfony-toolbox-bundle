<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\Service\StringTools;

abstract class AbstractDatatable
{
    protected ?QueryBuilder $queryBuilder = null;

    /**
     * Columns definitions. Each column is represented by an array.
     * Example:
     * [['name' => 'id', 'label' => 'Identifier', 'searchable' => true, 'sortable' => true,], ['name' => 'label', 'label' => 'Name', 'searchable' => true, 'sortable' => true,],].
     *
     * @var array<int, array<string, string|bool>>
     */
    protected array $columns = [];

    /**
     * Datatable options.
     * Example :
     * ['defaultPageSize' => 10, 'defaultSort' => 'id', 'defaultSortDirection' => 'asc', ].
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

    public function getMainAlias(): string
    {
        return $this->mainAlias;
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
     * Adds a column configuration to the columns array.
     *
     * @param string $name       the name of the column
     * @param string $label      the label of the column
     * @param bool   $searchable indicates whether the column is searchable
     * @param bool   $sortable   indicates whether the column is sortable
     */
    public function addColumn(string $name, string $label, bool $searchable = true, bool $sortable = true): self
    {
        $this->columns[] = [
            'name' => $name,
            'label' => $label,
            'searchable' => $searchable,
            'sortable' => $sortable,
        ];

        return $this;
    }

    /**
     * Validates the columns of the table configuration.
     *
     * Ensures each column has a "name" and a "label". Sets default values for
     * "searchable" and "sortable" attributes if they are not defined.
     *
     * @throws \InvalidArgumentException if a column does not have the required "name" and "label"
     */
    public function validateColumns(): void
    {
        foreach ($this->getColumns() as $column) {
            if (!isset($column['name'], $column['label'])) {
                throw new \InvalidArgumentException('Each column must have a "name" and a "label".');
            }
            if (!isset($column['searchable'])) {
                $column['searchable'] = true; // Default to true if not defined
            }
            if (!isset($column['sortable'])) {
                $column['sortable'] = true; // Default to true if not defined
            }
        }
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
            case 'searchable':
            case 'sortable':
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

    public function setQueryBuilder(QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    public function applySearch(QueryBuilder $queryBuilder, string $search): void
    {
        // Exemple : Rechercher sur des colonnes spécifiques.
        //  $queryBuilder->andWhere('entity.name LIKE :search')
        //      ->setParameter('search', "%$search%");
    }

    abstract public function configure(): array;

    public function buildQueryBuilder(): QueryBuilder
    {
        if (null === $this->queryBuilder) {
            $this->queryBuilder = $this->em->createQueryBuilder()
                ->select('t')
                ->from($this->getEntityClass(), 't');
        }

        return $this->queryBuilder;
    }

    /**
     * Gets the fully qualified class name of the main entity.
     *
     * This abstract method should be implemented by subclasses to return the
     * class name of the specific main entity they are related to.
     *
     * @return string The fully qualified class name of the entity
     */
    abstract protected function getEntityClass(): string;
}
