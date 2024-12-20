<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnDTO;

class AbstractDatatableTest extends TestCase
{
    private AbstractDatatable $datatable;

    protected function setUp(): void
    {
        // Instancie une classe concrète héritant d'AbstractDatatable pour les tests
        $em = $this->createMock(EntityManagerInterface::class);
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
    }

    public function testGetEntityClass(): void
    {
        $this->assertEquals('App\Entity\MyEntity', $this->datatable->getEntityClass());
    }

    public function testconfigure(): void
    {
        $this->assertInstanceOf(AbstractDatatable::class, $this->datatable->configure());
    }

    public function testGetEntityManager(): void
    {
        $this->assertInstanceOf(EntityManagerInterface::class, $this->datatable->getEntityManager());
    }

    public function testSetAndGetColumns(): void
    {
        $columns = [
            [
                'name' => 'id',
                'label' => 'Identifier',
                'searchable' => true,
                'sortable' => true,
                'header' => [
                    'translate' => true,
                    'keep_default_classes' => true,
                    'class' => 'myCssClasses',
                    'data' => ['custom-dataname' => 'myValue'],
                ],
            ],
        ];

        $this->datatable->setColumns($columns);
        $this->assertSame($columns, $this->datatable->getColumns());
    }

    public function testValidateColumnsAddsDefaults(): void
    {
        $columns = [
            ColumnDTO::fromArray([
                'name' => 'id',
                'label' => 'Identifier',
            ]),
        ];

        $this->datatable->setColumns($columns);
        $this->datatable->validateColumns();

        $validatedColumns = $this->datatable->getColumns();

        $this->assertTrue($validatedColumns[0]->searchable);
        $this->assertTrue($validatedColumns[0]->sortable);
    }

    public function testValidateColumns(): void
    {
        $columns = [
            ColumnDTO::fromArray([
                'label' => 'name',
                'name' => 'name',
            ]),
        ];

        $this->datatable->setColumns($columns);
        $this->datatable->validateColumns();
        $this->assertFalse($this->datatable->getDisplayFooter());
    }

    public function testGetFullyQualifiedColumnFromNameAs(): void
    {
        $columns = [
            ColumnDTO::fromArray([
                'name' => 'id',
                'label' => 'Identifier',
                'nameAs' => 'custom_id',
            ]),
        ];

        $this->datatable->setColumns($columns);
        $this->datatable->validateColumns();

        $this->assertEquals('t', $this->datatable->getMainAlias());

        $result = $this->datatable->getFullyQualifiedColumnFromNameAs('custom_id', true);
        $this->assertEquals('t.id AS custom_id', $result);

        $result = $this->datatable->getFullyQualifiedColumnFromNameAs('custom_id');
        $this->assertEquals('t.id', $result);

        $this->datatable->setMainAlias('e');
        // $result = $this->datatable->getFullyQualifiedColumnFromNameAs('custom_id');
        // $this->assertEquals('e.id', $result);
        $this->assertEquals('e', $this->datatable->getMainAlias());
    }

    public function testGetStimulusControllerName(): void
    {
        $this->assertEquals('zhortein--symfony-toolbox-bundle--datatable', $this->datatable->getStimulusControllerName());
    }

    public function testSetCssMode(): void
    {
        $this->datatable->setCssMode(Configuration::DATATABLE_CSS_MODE_BOOTSTRAP);
        $this->assertEquals(Configuration::DATATABLE_CSS_MODE_BOOTSTRAP, $this->datatable->getCssMode());

        $this->datatable->setCssMode(Configuration::DATATABLE_CSS_MODE_TAILWIND);
        $this->assertEquals(Configuration::DATATABLE_CSS_MODE_TAILWIND, $this->datatable->getCssMode());

        $this->datatable->setCssMode('invalid');
        $this->assertEquals(Configuration::DEFAULT_DATATABLE_CSS_MODE, $this->datatable->getCssMode());
    }

    public function testGetDefaultSort(): void
    {
        $this->assertSame([['field' => '', 'order' => 'asc']], $this->datatable->getDefaultSort());
    }

    public function testGlobalOptions(): void
    {
        $this->assertTrue($this->datatable->isIconUxMode());
        $this->assertEquals('bi:chevron-double-left', $this->datatable->getIcon('icon_first'));
        $this->assertEquals('bi:chevron-left', $this->datatable->getIcon('icon_previous'));
        $this->assertEquals('bi:chevron-right', $this->datatable->getIcon('icon_next'));
    }
}
