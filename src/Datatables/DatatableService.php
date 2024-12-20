<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

use Doctrine\ORM\QueryBuilder;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnDTO;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\DatatableManager;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\ExportCsvService;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\ExportExcelService;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\ExportPdfService;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorFactory;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorInterface;

class DatatableService
{
    private ?PaginatorInterface $paginator = null;

    public function __construct(
        private readonly DatatableManager $datatableManager,
        private readonly Environment $twig,
        private readonly PaginatorFactory $paginatorFactory,
        private readonly GotenbergPdfInterface $gotenbergPdf,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getGotenbergPdf(): GotenbergPdfInterface
    {
        return $this->gotenbergPdf;
    }

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function findDatatableById(string $id): ?AbstractDatatable
    {
        return $this->datatableManager->getDatatable($id);
    }

    public function getPaginator(): PaginatorInterface
    {
        if ($this->paginator instanceof PaginatorInterface) {
            return $this->paginator;
        }

        $defaultPaginator = $this->datatableManager->getGlobalOptions()->paginator ?? Configuration::DEFAULT_DATATABLE_PAGINATOR;

        return $this->paginatorFactory->createPaginator($defaultPaginator);
    }

    /**
     * Retrieves parameters from a given request and datatable.
     *
     * @param AbstractDatatable $datatable the datatable instance from which parameters are extracted
     * @param Request           $request   the HTTP request containing the parameters
     *
     * @return array{
     *      page: int,
     *      limit: int,
     *      search: string|null,
     *      multiSort: array<int, array{field: string, order: string}>
     *  } An array of extracted parameters
     */
    public function getParameters(AbstractDatatable $datatable, Request $request): array
    {
        return $this->extractParameters($request, $datatable);
    }

    /**
     * Extracts parameters from the given request and datatable.
     *
     * This method processes the request object and extracts pagination, sorting,
     * and search parameters to be used by the datatable.
     *
     * @param Request           $request   the HTTP request object containing query parameters
     * @param AbstractDatatable $datatable the datatable instance providing default sort options
     *
     * @return array{
     *     page: int<1, max>,
     *     limit: int<1, max>,
     *     search: string|null,
     *     multiSort: array<int, array{field: string, order: string}>,
     *     filters: array<int, array{
     *         column: string,
     *         type: string,
     *         value1: string,
     *         value2?: string,
     *         values?: array<int, array{key: string|int, label: string}>
     *     }>
     * } Associative array containing extracted parameters:
     *               - 'page' (int): The current page number, default is 1.
     *               - 'limit' (int): The number of items per page, default is determined by datatable options or global configuration.
     *               - 'multiSort' (array): An array defining multi-sort order; falls back to default sort if not provided.
     *               - 'filters' (array): An array defining filters; falls back to empty array if not provided.
     *               - 'search' (string|null): The search query string provided in the request.
     */
    private function extractParameters(Request $request, AbstractDatatable $datatable): array
    {
        // Get default sorting
        $defaultSort = $datatable->getDefaultSort();

        // Get multiSort and filters (search builder) from the QueryString
        $multiSort = [];
        $filters = [];
        foreach ($request->query->all() as $key => $value) {
            if ('multiSort' === $key) {
                $multiSort = $value;
            }
            if ('filters' === $key && '[]' !== $value) {
                /**
                 * @var array<int, array{
                 *          column: string,
                 *          type: string,
                 *          value1: string,
                 *          value2?: string,
                 *          values?: array<int, array{key: string|int, label: string}>
                 *      }> $filters
                 */
                $filters = $value;
            }
        }

        // Validate and clean multiSort
        $validatedMultiSort = [];
        if (is_array($multiSort)) {
            foreach ($multiSort as $sort) {
                if (is_array($sort) && isset($sort['field'], $sort['order']) && is_string($sort['field']) && in_array(
                    $sort['order'],
                    ['asc', 'desc'],
                    true
                )) {
                    $validatedMultiSort[] = [
                        'field' => $sort['field'],
                        'order' => $sort['order'],
                    ];
                }
            }
        }

        // Use received validated multiSort or default sorting
        if (empty($validatedMultiSort)) {
            $validatedMultiSort = $defaultSort;
        }

        $defaultPageSize = $datatable->getOptions()->defaultPageSize;
        if ($defaultPageSize < 1) {
            $defaultPageSize = $this->datatableManager->getGlobalOptions()->itemsPerPage ?? Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
            if ($defaultPageSize < 1) {
                $defaultPageSize = Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE;
            }
        }

        return [
            'page' => max(1, (int) $request->query->get('page', '1')),
            'limit' => max(1, (int) $request->query->get('limit', (string) $defaultPageSize)),
            'multiSort' => $validatedMultiSort,
            'search' => $request->query->get('search'),
            'filters' => $filters,
        ];
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws \JsonException
     */
    public function render(AbstractDatatable $datatable, Request $request): Response
    {
        $translationDomain = AbstractDatatable::DEFAULT_TRANSLATION_DOMAIN;
        $this->validateDatatable($datatable);
        $data = $this->processRequest($datatable, $request);

        // Render Rows and Pagination
        $htmlRows = $this->twig->render('@ZhorteinSymfonyToolbox/datatables/_rows-'.$datatable->getCssMode().'.html.twig', [
            'data' => $data,
            'datatable' => $datatable,
            'transDomain' => $translationDomain,
        ]);
        $htmlPagination = $this->twig->render('@ZhorteinSymfonyToolbox/datatables/_pagination-'.$datatable->getCssMode().'.html.twig', [
            'data' => $data,
            'datatable' => $datatable,
            'transDomain' => $translationDomain,
        ]);

        return new Response(json_encode([
            'rows' => $htmlRows,
            'pagination' => $htmlPagination,
            'icons' => [
                'icon_sort_asc' => $this->twig->render('@ZhorteinSymfonyToolbox/datatables/icons/_icon.html.twig', ['icon' => 'icon_sort_asc', 'datatable' => $datatable]),
                'icon_sort_desc' => $this->twig->render('@ZhorteinSymfonyToolbox/datatables/icons/_icon.html.twig', ['icon' => 'icon_sort_desc', 'datatable' => $datatable]),
                'icon_sort_neutral' => $this->twig->render('@ZhorteinSymfonyToolbox/datatables/icons/_icon.html.twig', ['icon' => 'icon_sort_neutral', 'datatable' => $datatable]),
            ],
        ], JSON_THROW_ON_ERROR), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @return array{
     *      total: int,
     *      filtered: int,
     *      data: array<int, mixed>,
     *      pagination: array{
     *        current: int,
     *        hasPrevious: bool,
     *        nbPages: int,
     *        previous: int,
     *        pages: int[],
     *        pageSize: int,
     *        hasNext: bool,
     *        next: int,
     *   }
     *  }
     *
     * @throws \InvalidArgumentException
     */
    private function processRequest(AbstractDatatable $datatable, Request $request): array
    {
        $params = $this->extractParameters($request, $datatable);
        $queryBuilder = $this->handleRequest($request, $datatable);

        $paginatorMode = $datatable->getOptions()->paginator ?? ($this->datatableManager->getGlobalOptions()->paginator ?? Configuration::DEFAULT_DATATABLE_PAGINATOR);
        $this->paginator = is_string($paginatorMode) ? $this->paginatorFactory->createPaginator($paginatorMode) : $this->getPaginator();
        $results = $this->paginator->paginate($queryBuilder, $params['page'], $params['limit']);
        $total = $results->getTotalItemCount();

        $nbPages = (int) ceil($total / $params['limit']);
        $pagination = [
            'current' => $params['page'],
            'hasPrevious' => $nbPages > 1 && $params['page'] > 1,
            'nbPages' => $nbPages,
            'previous' => $nbPages > 1 && $params['page'] > 1 ? $params['page'] - 1 : 1,
            'pages' => range(1, $nbPages),
            'pageSize' => $params['limit'],
            'hasNext' => $nbPages > 1 && $params['page'] < $nbPages,
            'next' => $nbPages > 1 && $params['page'] < $nbPages ? $params['page'] + 1 : $params['page'],
        ];

        return (new DatatableResponse(
            total: count((array) $queryBuilder->getQuery()->getResult()), // Total without filters.
            filtered: count($results->getItems()), // Total after pagination and filters.
            data: $results->getItems(),
            pagination: $pagination
        ))->toArray();
    }

    private function validateDatatable(AbstractDatatable $datatable): void
    {
        if (empty($datatable->getColumns())) {
            throw new \InvalidArgumentException('The datatable must define at least one column.');
        }

        $datatable->validateColumns();
    }

    /**
     * Validates whether the specified sort field is sortable and matches a column's name.
     *
     * @param string      $sortField the field name to validate
     * @param ColumnDTO[] $columns   An array of columns to check against, each containing 'nameAs' and 'sortable' keys
     *
     * @return bool returns true if the sort field is valid and the corresponding column is sortable, false otherwise
     */
    private function validateSortField(string $sortField, array $columns): bool
    {
        foreach ($columns as $column) {
            if (($column->nameAs ?? '') === $sortField && ($column->sortable ?? true)) {
                return true;
            }
        }

        return false;
    }

    public function export(AbstractDatatable $datatable, Request $request, string $type): Response
    {
        $queryBuilder = $this->handleRequest($request, $datatable);
        $exportService = match ($type) {
            'csv' => new ExportCsvService($queryBuilder, $this->translator),
            'excel' => new ExportExcelService($queryBuilder, $this->translator),
            'pdf' => (new ExportPdfService($queryBuilder, $this->translator))->setGotenbergClient($this->gotenbergPdf),
            default => throw new \InvalidArgumentException(sprintf('Unsupported export type "%s".', $type)),
        };

        return $exportService->export($datatable, $request);
    }

    private function handleRequest(Request $request, AbstractDatatable $datatable): QueryBuilder
    {
        $params = $this->extractParameters($request, $datatable);
        $queryBuilder = $datatable->getQueryBuilder();

        if ($params['search'] && $datatable->isSearchable()) {
            $datatable->applySearch($queryBuilder, $params['search']);
        }
        if (!empty($params['filters']) && $datatable->isSearchable()) {
            $datatable->applyFilters($queryBuilder, $params['filters']);
        }
        $datatable->applyStaticFilters($queryBuilder);

        if ($datatable->isSortable() && count($params['multiSort']) > 0) {
            foreach ($params['multiSort'] as $sort) {
                if ($sort['field'] && !$this->validateSortField($sort['field'], $datatable->getColumns())) {
                    throw new \InvalidArgumentException(sprintf('Invalid sort field "%s".', $sort['field']));
                }

                if (!empty($sort['field'])) {
                    $queryBuilder->addOrderBy(
                        $datatable->getFullyQualifiedColumnFromNameAs($sort['field']),
                        $sort['order']
                    );
                }
            }
        }

        return $queryBuilder;
    }
}
