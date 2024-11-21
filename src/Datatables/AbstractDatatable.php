<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractDatatable
{
    protected ?QueryBuilder $queryBuilder = null;
    protected array $columns = [];
    protected array $options = [];

    public function __construct(protected EntityManagerInterface $em)
    {
        $this->configure();
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

    public function validateColumns(): void
    {
        foreach ($this->getColumns() as $column) {
            if (!isset($column['name'], $column['label'])) {
                throw new \InvalidArgumentException('Each column must have a "name" and a "label".');
            }
            if (!isset($column['searchable'])) {
                $column['searchable'] = true; // Default to true if not defined
            }
            if (!isset($column['orderable'])) {
                $column['orderable'] = true; // Default to true if not defined
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
        return $this->options;
    }

    /**
     * @return array<string, string|null>
     */
    public function getDefaultSort(): array
    {
        return $this->getOptions()['defaultSort'] ?? ['column' => null, 'order' => 'asc'];
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

    abstract protected function getEntityClass(): string;
}
