<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\DatatableOptionsDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\GlobalOptionsDTO;
use Zhortein\SymfonyToolboxBundle\Service\Cache\CacheManager;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\DatatableManager;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorFactory;

class DatatableManagerTest extends TestCase
{
    private DatatableService $datatableService;
    private AbstractDatatable $datatable;

    private DatatableManager $datatableManager;
    private CacheManager $cacheManager;

    protected function setUp(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $twigMock = $this->createMock(Environment::class);
        $paginatorFactoryMock = $this->createMock(PaginatorFactory::class);
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $gotenbergMock = $this->createMock(GotenbergPdfInterface::class);
        $this->cacheManager = $this->createMock(CacheManager::class);

        $this->datatable = new class($em) extends AbstractDatatable {
            public function configure(): AbstractDatatable
            {
                return $this;
            }

            public function getEntityClass(): string
            {
                return 'App\Entity\MyEntity';
            }

            public function calculateChecksum(string $name): string
            {
                return 'checksum_123'; // Retour simulé pour les tests
            }

            public function getColumns(): array
            {
                return [
                    ColumnDTO::fromArray(['name' => 'id', 'label' => 'ID', 'nameAs' => 'id']),
                    ColumnDTO::fromArray(['name' => 'name', 'label' => 'Name', 'nameAs' => 'name']),
                ];
            }

            public function getQueryBuilder(): QueryBuilder
            {
                // Simuler un QueryBuilder si nécessaire
                return new class {
                    public function setMaxResults(int $max)
                    {
                        return $this; // Simuler le chaînage de méthodes
                    }

                    public function getQuery()
                    {
                        return new class {
                            public function getArrayResult()
                            {
                                return [['id' => 1, 'name' => 'Test']]; // Données simulées
                            }
                        };
                    }
                };
            }
        };

        $this->datatableManager = new DatatableManager(
            ['dt1' => $this->datatable],
            [
                'dt1' => [
                    ['name' => 'id', 'label' => 'ID', 'nameAs' => 'id'],
                    ['name' => 'name', 'label' => 'Name', 'nameAs' => 'name'],
                ],
            ],
            ['dt1' => [
                    'name' => 'testDatatable',
                    'defaultPageSize' => 15,
                ],
            ],
            Configuration::DEFAULT_CONFIGURATION,
            $this->cacheManager
        );

        $this->datatableService = new DatatableService($this->datatableManager, $twigMock, $paginatorFactoryMock, $gotenbergMock, $translatorMock);
    }

    public function testGetCssMode(): void
    {
        $this->assertEquals(Configuration::DEFAULT_DATATABLE_CSS_MODE, $this->datatableManager->getCssMode());
    }

    public function testGetDatatableOptions(): void
    {
        $this->assertEquals(DatatableOptionsDTO::fromArray([
            'name' => 'testDatatable',
            'defaultPageSize' => 15,
        ], GlobalOptionsDTO::fromArray(Configuration::DEFAULT_CONFIGURATION)), $this->datatableManager->getDatatableOptions('dt1'));
    }
}
