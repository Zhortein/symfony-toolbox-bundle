<?php

namespace Zhortein\SymfonyToolboxBundle\Datatables;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
        private DatatableManager $datatableManager,
        private readonly Environment $twig,
        private readonly PaginatorFactory $paginatorFactory,
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    private function getGlobalOption(string $key, mixed $default = null): mixed
    {
        return $this->parameterBag->get("zhortein_symfony_toolbox.datatables.$key") ?? $default;
    }

    public function findDatatableById(string $id): ?AbstractDatatable
    {
        return $this->datatableManager->getDatatable($id);
    }

    public function getPaginator(): object
    {
        return $this->paginator ?? $this->paginatorFactory->createPaginator($this->getGlobalOption('paginator', Configuration::DEFAULT_DATATABLE_PAGINATOR));
    }

    public function getParameters(AbstractDatatable $datatable, Request $request): array
    {
        return $this->extractParameters($request, $datatable);
    }

    private function extractParameters(Request $request, AbstractDatatable $datatable): array
    {
        $defaultSort = $datatable->getDefaultSort();

        return [
            'page' => max(1, (int) $request->query->get('page', 1)),
            'limit' => max(1, (int) $request->query->get('limit', $datatable->getOptions()['defaultPageSize'] ?? $this->getGlobalOption('items_per_page', Configuration::DEFAULT_DATATABLE_ITEMS_PER_PAGE))),
            'sort' => $request->query->get('sort', $defaultSort['column']),
            'order' => $request->query->get('order', $defaultSort['order']),
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
        $this->validateDatatable($datatable);
        $data = $this->processRequest($datatable, $request);

        // Render Rows and Pagination
        $htmlRows = $this->twig->render('@ZhorteinSymfonyToolbox/datatables/_rows.html.twig', ['data' => $data]);
        $htmlPagination = $this->twig->render('@ZhorteinSymfonyToolbox/datatables/_pagination.html.twig', ['data' => $data]);

        return new Response(json_encode([
            'rows' => $htmlRows,
            'pagination' => $htmlPagination,
        ], JSON_THROW_ON_ERROR), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    private function processRequest(AbstractDatatable $datatable, Request $request): array
    {
        $params = $this->extractParameters($request, $datatable);
        $queryBuilder = $datatable->buildQueryBuilder();

        if ($params['search']) {
            $datatable->applySearch($queryBuilder, $params['search']);
        }

        if ($params['sort'] && !$this->validateSortField($params['sort'], $datatable->getColumns())) {
            throw new \InvalidArgumentException(sprintf('Invalid sort field "%s".', $params['sort']));
        }

        if ($params['sort'] && $params['order']) {
            $queryBuilder->orderBy($params['sort'], $params['order']);
        }

        $paginatorMode = $datatable->getOptions()['paginator'] ?? $this->getGlobalOption('paginator', Configuration::DEFAULT_DATATABLE_PAGINATOR);
        $this->paginator = $this->paginatorFactory->createPaginator($paginatorMode);
        $results = $this->paginator->paginate($queryBuilder, $params['page'], $params['limit']);

        return (new DatatableResponse(
            total: count($queryBuilder->getQuery()->getResult() ?? []), // Total without filters.
            filtered: count($results), // Total after pagination and filters.
            data: $results
        ))->toArray();
    }

    private function validateDatatable(AbstractDatatable $datatable): void
    {
        if (empty($datatable->getColumns())) {
            throw new \InvalidArgumentException('The datatable must define at least one column.');
        }

        foreach ($datatable->getColumns() as $column) {
            if (!isset($column['name'], $column['label'])) {
                throw new \InvalidArgumentException('Each column must have a "name" and a "label".');
            }
        }
    }

    private function validateSortField(string $sortField, array $columns): bool
    {
        foreach ($columns as $column) {
            if ($column['name'] === $sortField && $column['orderable']) {
                return true;
            }
        }

        return false;
    }
}
