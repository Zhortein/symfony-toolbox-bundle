<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

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

            $datatable->validateColumns();
            $datatable->validateTableOptions();

            // Get cached column_types
            $cachedTypes = $this->cache->get($checksum, function () use ($datatable) {
                return $this->buildDatatableTypesForCache($datatable);
            });

            // Load column_types in datatable columns
            $datatable->setCachedTypes($cachedTypes);
        }

        return $datatable;
    }

    private function buildDatatableTypesForCache(AbstractDatatable $datatable): array
    {
        // Lancer la requête avec LIMIT 1
        $query = $datatable->getQueryBuilder()
            ->setMaxResults(1)
            ->getQuery();
        $result = $query->getArrayResult();

        $types = [];
        if (!empty($result[0])) {
            foreach ($result[0] as $key => $value) {
                // Détection du type PHP
                $detectedType = gettype($value);
                $isEnum = false;
                $isTranslatableEnum = false;
                if ('object' === $detectedType) {
                    $detectedType = get_class($value);
                    $isEnum = $value instanceof \BackedEnum;
                    $isTranslatableEnum = method_exists($value, 'label') && method_exists($value, 'getTranslationDomain');
                }

                foreach ($datatable->getColumns() as $rank => $column) {
                    if ($column['nameAs'] === $key) {
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
