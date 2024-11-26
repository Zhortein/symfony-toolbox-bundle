<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\DatatableManager;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorFactory;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorInterface;

class DatatableService
{
    private PaginatorInterface $paginator;

    public function __construct(
        private readonly DatatableManager $datatableManager,
        private readonly Environment $twig,
        private readonly PaginatorFactory $paginatorFactory,
    ) {
    }

    public function findDatatableById(string $id): ?AbstractDatatable
    {
        return $this->datatableManager->getDatatable($id);
    }

    public function getPaginator(): object
    {
        return $this->paginator ?? $this->paginatorFactory->createPaginator($this->datatableManager->getGlobalOption('paginator', Configuration::DEFAULT_DATATABLE_PAGINATOR));
    }

    public function getParameters(AbstractDatatable $datatable, Request $request): array
    {
        return $this->extractParameters($request, $datatable);
    }

    private function extractParameters(Request $request, AbstractDatatable $datatable): array
    {
        $defaultSort = $datatable->getDefaultSort();
        $sortValue = $request->query->get('sort', $defaultSort['column']);
        if ('null' === $sortValue) {
            $sortValue = $defaultSort['column'];
        }
        $orderValue = strtolower($request->query->get('order', $defaultSort['order']));
        if (!in_array($orderValue, ['asc', 'desc'], true)) {
            $orderValue = $defaultSort['order'];
        }

        return [
            'page' => max(1, (int) $request->query->get('page', 1)),
            'limit' => max(1, (int) $request->query->get('limit', $datatable->getOptions()['defaultPageSize'] ?? $this->datatableManager->getGlobalOption('items_per_page', Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE))),
            'sort' => $sortValue,
            'order' => $orderValue,
            'search' => $request->query->get('search', null),
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

    private function processRequest(AbstractDatatable $datatable, Request $request): array
    {
        $params = $this->extractParameters($request, $datatable);
        $queryBuilder = $datatable->getQueryBuilder();

        if ($params['search'] && $datatable->isSearchable()) {
            $datatable->applySearch($queryBuilder, $params['search']);
        }

        if ($datatable->isSortable()) {
            if ($params['sort'] && !$this->validateSortField($params['sort'], $datatable->getColumns())) {
                throw new \InvalidArgumentException(sprintf('Invalid sort field "%s".', $params['sort']));
            }

            if ($params['sort'] && $params['order']) {
                $queryBuilder->orderBy(
                    $datatable->getColumnAlias($params['sort']).'.'.$params['sort'],
                    $params['order']
                );
            }
        }

        $paginatorMode = $datatable->getOptions()['paginator'] ?? $this->datatableManager->getGlobalOption('paginator', Configuration::DEFAULT_DATATABLE_PAGINATOR);
        $this->paginator = $this->paginatorFactory->createPaginator($paginatorMode);
        $results = $this->paginator->paginate($queryBuilder, $params['page'], $params['limit']);
        $total = $this->paginator->getTotal($queryBuilder);

        $nbPages = ceil($total / $params['limit']);
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
            total: count($queryBuilder->getQuery()->getResult() ?? []), // Total without filters.
            filtered: count($results), // Total after pagination and filters.
            data: $results,
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

    private function validateSortField(string $sortField, array $columns): bool
    {
        foreach ($columns as $column) {
            if ($column['name'] === $sortField && $column['sortable']) {
                return true;
            }
        }

        return false;
    }
}
