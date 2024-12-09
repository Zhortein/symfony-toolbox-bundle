<?php

namespace Zhortein\SymfonyToolboxBundle\Service\Datatables;

use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnCachedTypeDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\DatatableOptionsDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\GlobalOptionsDTO;
use Zhortein\SymfonyToolboxBundle\Service\Cache\CacheManager;

readonly class DatatableManager
{
    /**
     * @param array<string, AbstractDatatable>   $datatables
     * @param array<string, ColumnDTO[]>         $datatableColumns
     * @param array<string, DatatableOptionsDTO> $datatableOptions
     */
    public function __construct(
        private array $datatables,
        private array $datatableColumns,
        private array $datatableOptions,
        private GlobalOptionsDTO $globalOptions,
        private CacheManager $cache,
    ) {
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
                    if (($column->nameAs ?? '') === $key) {
                        $types[$key] = new ColumnCachedTypeDTO(
                            rank: $rank,
                            datatype: $detectedType,  // Type détecté
                            isEnum: $isEnum,
                            isTranslatableEnum: $isTranslatableEnum,
                        );
                        break;
                    }
                }
            }
        }

        return $types;
    }
}
