<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Service;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableResponse;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\CustomPaginatorAdapter;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\KnpPaginatorAdapter;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorFactory;

class DatatableTest extends TestCase
{
    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->paginatorFactory = $this->createMock(PaginatorFactory::class);
        // Utilisation d'un vrai ContainerBuilder pour les tests
        $this->container = new ContainerBuilder();

        // Enregistrer un service fictif pour la datatable
        $datatableMock = $this->getMockForAbstractClass(AbstractDatatable::class);
        $this->container->set('mock.service.id', $datatableMock);
        $this->container->setParameter('mock.datatable', [['id' => 'mock-datatable']]);
    }

    public function testGetParameters(): void
    {
        $request = new Request([
            'page' => 2,
            'limit' => 15,
            'sort' => 'name',
            'order' => 'desc',
            'search' => 'test',
        ]);

        $datatableMock = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatableMock->setOptions(['defaultPageSize' => 10, 'defaultSort' => ['column' => 'id', 'order' => 'asc']]);

        $service = new DatatableService($this->container, $this->twig, $this->paginatorFactory, ['datatables' => ['paginator' => 'custom']]);
        $parameters = $service->getParameters($datatableMock, $request);

        $this->assertEquals(2, $parameters['page']);
        $this->assertEquals(15, $parameters['limit']);
        $this->assertEquals('name', $parameters['sort']);
        $this->assertEquals('desc', $parameters['order']);
        $this->assertEquals('test', $parameters['search']);
    }

    public function testPaginatorSelection(): void
    {
        $customPaginator = $this->createMock(CustomPaginatorAdapter::class);
        $knpPaginator = $this->createMock(KnpPaginatorAdapter::class);

        $paginatorFactoryMock = $this->createMock(PaginatorFactory::class);
        $paginatorFactoryMock->method('createPaginator')
            ->willReturnOnConsecutiveCalls($customPaginator, $knpPaginator, $customPaginator);

        $request = new Request();

        // Mock datatable with custom paginator
        $datatableMock = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatableMock->setColumns([
            ['name' => 'id', 'label' => 'ID', 'searchable' => true, 'sortable' => true],
        ]);
        $datatableMock->setOptions(['paginator' => PaginatorFactory::PAGINATOR_CUSTOM]);

        $service = new DatatableService($this->container, $this->twig, $paginatorFactoryMock, ['datatables' => ['paginator' => PaginatorFactory::PAGINATOR_CUSTOM]]);
        $service->render($datatableMock, $request);
        $this->assertSame($customPaginator, $service->getPaginator(), 'Should use CustomPaginator when specified.');

        // Mock datatable with KNP paginator
        $datatableMock->setOptions(['paginator' => PaginatorFactory::PAGINATOR_KNP]);

        $service = new DatatableService($this->container, $this->twig, $paginatorFactoryMock, ['datatables' => ['paginator' => PaginatorFactory::PAGINATOR_KNP]]);
        $service->render($datatableMock, $request);
        $this->assertSame($knpPaginator, $service->getPaginator(), 'Should use KnpPaginator when specified.');

        // Mock datatable with no explicit paginator (should fallback to default from configuration)
        $datatableMock->setOptions([]);
        $service = new DatatableService($this->container, $this->twig, $paginatorFactoryMock, ['datatables' => ['paginator' => PaginatorFactory::PAGINATOR_CUSTOM]]);
        $service->render($datatableMock, $request);
        $this->assertSame($customPaginator, $service->getPaginator(), 'Should fallback to default paginator from configuration.');
    }

    public function testSetAndGetColumns(): void
    {
        $datatable = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatable->setColumns([
            ['name' => 'id', 'label' => 'ID', 'searchable' => true, 'sortable' => true],
        ]);

        $this->assertCount(1, $datatable->getColumns());
        $this->assertSame('ID', $datatable->getColumns()[0]['label']);
    }

    public function testMissingColumnConfiguration(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $datatable = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatable->setColumns([
            ['name' => 'id'], // Missing 'label'
        ]);

        $datatable->validateColumns();
    }

    public function testExtractParameters(): void
    {
        $request = new Request([
            'page' => 2,
            'limit' => 15,
            'sort' => 'name',
            'order' => 'desc',
            'search' => 'test',
        ]);

        $datatableMock = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatableMock->setColumns([
            ['name' => 'id', 'label' => 'ID', 'searchable' => true, 'sortable' => true],
        ]);
        $datatableMock->setOptions(['defaultPageSize' => 10, 'defaultSort' => ['column' => 'id', 'order' => 'asc']]);

        $service = new DatatableService($this->container, $this->twig, $this->paginatorFactory, []);
        $parameters = $service->getParameters($datatableMock, $request);

        $this->assertEquals(2, $parameters['page']);
        $this->assertEquals(15, $parameters['limit']);
        $this->assertEquals('name', $parameters['sort']);
        $this->assertEquals('desc', $parameters['order']);
        $this->assertEquals('test', $parameters['search']);
    }

    public function testRender(): void
    {
        // Configure Twig
        $loader = new ArrayLoader([
            '@ZhorteinSymfonyToolbox/datatables/_rows.html.twig' => '<tr><td>Mock Table rows</td></tr>',
            '@ZhorteinSymfonyToolbox/datatables/_pagination.html.twig' => '<nav><ul class="pagination"><li>Mock Table Pagination</li></ul></nav>',
        ]);
        $twig = new Environment($loader);

        // Mock dependencies
        $datatableMock = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatableMock->method('configure')->willReturn(['columns' => [['name' => 'id', 'label' => 'ID']]]);

        $datatableMock->setColumns([
            ['name' => 'id', 'label' => 'ID', 'searchable' => true, 'sortable' => true],
        ]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $datatableMock->method('buildQueryBuilder')->willReturn($queryBuilder);

        $paginatorFactory = $this->createMock(PaginatorFactory::class);

        // Instantiate the service
        $service = new DatatableService($this->container, $twig, $paginatorFactory, []);

        // Make the request
        $request = new Request();
        $response = $service->render($datatableMock, $request);

        // Assertions
        $this->assertStringContainsString('<nav', $response->getContent());
    }

    public function testDatatableColumns(): void
    {
        $datatable = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatable->setColumns([
            ['name' => 'id', 'label' => 'ID', 'searchable' => true, 'sortable' => true],
            ['name' => 'name', 'label' => 'Name', 'searchable' => false, 'sortable' => false],
        ]);

        $this->assertEquals('ID', $datatable->getColumns()[0]['label']);
        $this->assertEquals('name', $datatable->getColumns()[1]['name']);
        $this->assertTrue($datatable->getColumns()[0]['searchable']);
        $this->assertFalse($datatable->getColumns()[1]['searchable']);
        $this->assertTrue($datatable->getColumns()[0]['sortable']);
        $this->assertFalse($datatable->getColumns()[1]['sortable']);
    }

    public function testDatatableConfiguration(): void
    {
        $datatable = $this->getMockForAbstractClass(AbstractDatatable::class);
        $datatable->setColumns([
            ['name' => 'id', 'label' => 'ID', 'searchable' => true, 'sortable' => false],
            ['name' => 'name', 'label' => 'Name', 'searchable' => false, 'sortable' => true],
        ]);

        $this->assertCount(2, $datatable->getColumns());
        $this->assertCount(4, $datatable->getColumns()[0]);
        $this->assertEquals('id', $datatable->getColumns()[0]['name']);
        $this->assertEquals('ID', $datatable->getColumns()[0]['label']);
        $this->assertTrue($datatable->getColumns()[0]['searchable']);
        $this->assertFalse($datatable->getColumns()[0]['sortable']);
        $this->assertEquals('name', $datatable->getColumns()[1]['name']);
        $this->assertEquals('Name', $datatable->getColumns()[1]['label']);
        $this->assertFalse($datatable->getColumns()[1]['searchable']);
        $this->assertTrue($datatable->getColumns()[1]['sortable']);
    }

    public function testToArray(): void
    {
        $response = new DatatableResponse(100, 50, [['id' => 1], ['id' => 2]]);
        $data = $response->toArray();

        $this->assertEquals(100, $data['total']);
        $this->assertEquals(50, $data['filtered']);
        $this->assertCount(2, $data['data']);
    }
}
