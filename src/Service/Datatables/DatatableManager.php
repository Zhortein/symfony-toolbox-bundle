<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\Service\Cache\CacheManager;

readonly class DatatableManager
{
    /**
     * @param array<string, AbstractDatatable> $datatables
     * @param array<string, array>             $datatableOptions
     */
    public function __construct(
        private array $datatables,
        private array $datatableOptions,
        private array $globalOptions,
        private CacheManager $cache,
    ) {
    }

    public function getGlobalOption(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->globalOptions) ? $this->globalOptions[$key] : $default;
    }

    public function getDatatableOptions(string $name): ?array
    {
        return $this->datatableOptions[$name] ?? null;
    }

    public function getDatatable(string $name): ?AbstractDatatable
    {
        $datatable = $this->datatables[$name] ?? null;
        if ($datatable instanceof AbstractDatatable) {
            $checksum = $datatable->calculateChecksum($name);
            $datatable->setGlobalOptions($this->globalOptions);
            $datatable->setCssMode($this->getGlobalOption('css_mode', Configuration::DEFAULT_DATATABLE_CSS_MODE));
            $options = $this->getDatatableOptions($name);
            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    if ('columns' === $key) {
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
                            );
                        }
                    }

                    if (in_array($key, ['defaultPageSize', 'defaultSort', 'searchable', 'sortable', 'options', 'autoColumns', 'translationDomain'])) {
                        $datatable->addOption($key, $value);
                    }
                }
            }

            // Get cached datatypes
            $cachedTypes = $this->cache->get($checksum, function () use ($datatable) {
                return $this->buildDatatableTypesForCache($datatable);
            });

            // Load datatypes in datatable columns
            // $datatable->setCachedTypes($cachedTypes);

            $datatable->validateColumns();
            $datatable->validateTableOptions();
        }

        return $datatable;
    }

    private function buildDatatableTypesForCache(AbstractDatatable $datatable): array
    {
        // Lancer la requête avec LIMIT 1
        $query = $datatable->buildQueryBuilder()
            ->setMaxResults(1)
            ->getQuery();
        $result = $query->getArrayResult();

        // Récupérer les alias et métadonnées
        $queryBuilder = $datatable->buildQueryBuilder();
        $rootAliases = $queryBuilder->getRootAliases(); // Alias racines
        $rootEntities = $queryBuilder->getRootEntities(); // Entités racines
        $joins = $queryBuilder->getDQLPart('join'); // Récupérer les jointures
        $entityMappings = $this->getEntityMappings($rootEntities, $joins, $datatable->getEntityManager());

        $types = [];
        if (!empty($result[0])) {
            foreach ($result[0] as $key => $value) {
                // Détection du type PHP
                $detectedType = gettype($value);
                if ('object' === $detectedType) {
                    $detectedType = get_class($value);
                }

                // Recherche dans les mappings pour retrouver le champ
                [$entityName, $fieldName] = $this->getFieldNameFromAlias($key, $entityMappings);

                $types[$key] = [
                    'entityName' => $entityName,
                    'name' => $fieldName ?: $key, // Nom du champ
                    'sqlAlias' => $key,           // Alias SQL utilisé
                    'datatype' => $detectedType,  // Type détecté
                ];
            }
        }

        return $types;
    }

    /**
     * Récupère les métadonnées des entités racines et jointes.
     *
     * @param array $rootEntities Entités racines
     * @param array $joins        Jointures
     */
    private function getEntityMappings(array $rootEntities, array $joins, EntityManagerInterface $entityManager): array
    {
        // Ajouter les entités racines
        $mappings = array_map(static function ($entity) use ($entityManager) {
            return $entityManager->getClassMetadata($entity);
        }, $rootEntities);

        // Ajouter les entités jointes
        foreach ($joins as $alias => $joinParts) {
            foreach ($joinParts as $join) {
                $joinAlias = $join->getAlias();
                $entityClass = $join->getJoin();
                $mappings[$joinAlias] = $entityManager->getClassMetadata($entityClass);
            }
        }

        return $mappings;
    }

    /**
     * Retrouve le champ d'une entité à partir d'un alias SQL.
     *
     * @return array [nom de l'entité, nom du champ]
     */
    private function getFieldNameFromAlias(string $sqlAlias, array $entityMappings): array
    {
        foreach ($entityMappings as $alias => $metadata) {
            if (str_starts_with($sqlAlias, $alias.'.')) {
                $field = str_replace($alias.'.', '', $sqlAlias);
                if ($metadata->hasField($field)) {
                    return [$metadata->getName(), $field];
                }
            }
        }

        return [null, null];
    }
}
