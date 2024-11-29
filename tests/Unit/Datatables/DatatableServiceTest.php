<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\Service\Cache\CacheManager;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\DatatableManager;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorFactory;

class DatatableServiceTest extends TestCase
{
    private DatatableService $datatableService;
    private AbstractDatatable $datatable;

    protected function setUp(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $cacheManager = $this->createMock(CacheManager::class);
        $twigMock = $this->createMock(Environment::class);
        $paginatorFactoryMock = $this->createMock(PaginatorFactory::class);

        $this->datatable = new class($em) extends AbstractDatatable {
            public function configure(): AbstractDatatable
            {
                return $this;
            }

            public function getEntityClass(): string
            {
                return 'App\Entity\MyEntity';
            }
        };

        $datatableManager = new DatatableManager(
            ['dt1' => $this->datatable],
            ['dt1' => [
                'defaultPageSize' => 20,
            ],
            ],
            Configuration::DEFAULT_CONFIGURATION,
            $cacheManager
        );

        $this->datatableService = new DatatableService($datatableManager, $twigMock, $paginatorFactoryMock);
    }

    public function testExtractParameters(): void
    {
        $request = new Request([
            'page' => 2,
            'limit' => 10,
            'search' => 'test',
            'multiSort' => [
                ['field' => 'name', 'order' => 'asc'],
                ['field' => 'id', 'order' => 'desc'],
            ],
        ]);

        $this->datatable->setColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'id', 'label' => 'Identifier'],
        ]);

        $params = $this->datatableService->getParameters($this->datatable, $request);

        $this->assertSame(2, $params['page']);
        $this->assertSame(10, $params['limit']);
        $this->assertSame('test', $params['search']);
        $this->assertCount(2, $params['multiSort']);
        $this->assertSame('name', $params['multiSort'][0]['field']);
        $this->assertSame('asc', $params['multiSort'][0]['order']);
    }

    public function testProcessRequestWithPagination(): void
    {
        $request = new Request([
            'page' => 1,
            'limit' => 5,
        ]);

        $this->datatable->setColumns([
            ['name' => 'id', 'label' => 'Identifier'],
        ]);

        // Mock des méthodes nécessaires pour simuler un fonctionnement complet
        $this->datatable->addOption('searchable', true);
        $this->datatable->addOption('sortable', true);

        $result = $this->datatableService->render($this->datatable, $request);
        $result = json_decode($result->getContent(), true);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('rows', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('icons', $result);
    }
}
