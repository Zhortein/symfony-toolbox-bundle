<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\Configuration;

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
            [
                'name' => 'id',
                'label' => 'Identifier',
            ],
        ];

        $this->datatable->setColumns($columns);
        $this->datatable->validateColumns();

        $validatedColumns = $this->datatable->getColumns();

        $this->assertArrayHasKey('searchable', $validatedColumns[0]);
        $this->assertTrue($validatedColumns[0]['searchable']);
        $this->assertArrayHasKey('sortable', $validatedColumns[0]);
        $this->assertTrue($validatedColumns[0]['sortable']);
    }

    public function testValidateColumnsThrowsExceptionForInvalidColumns(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each column must have a "name" and a "label".');

        $columns = [
            [
                'label' => 'Missing name',
            ],
        ];

        $this->datatable->setColumns($columns);
        $this->datatable->validateColumns();
    }

    public function testGetFullyQualifiedColumnFromNameAs(): void
    {
        $columns = [
            [
                'name' => 'id',
                'label' => 'Identifier',
                'nameAs' => 'custom_id',
            ],
        ];

        $this->datatable->setColumns($columns);
        $this->datatable->validateColumns();

        $this->datatable->addColumn('name', 'Name', true, false, 't2', [], [], [], 'name2');
        $result = $this->datatable->getFullyQualifiedColumnFromNameAs('name2', true);
        $this->assertEquals('t2.name AS name2', $result);

        $this->assertEquals('t', $this->datatable->getMainAlias());

        $result = $this->datatable->getFullyQualifiedColumnFromNameAs('custom_id', true);
        $this->assertEquals('t.id AS custom_id', $result);

        $result = $this->datatable->getFullyQualifiedColumnFromNameAs('custom_id');
        $this->assertEquals('t.id', $result);

        $this->datatable->setMainAlias('e');
        $result = $this->datatable->getFullyQualifiedColumnFromNameAs('custom_id');
        $this->assertEquals('e.id', $result);
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
        $this->assertEquals([['field' => '', 'order' => 'asc']], $this->datatable->getDefaultSort());
    }

    public function testOptions(): void
    {
        $this->assertSame([
            'defaultPageSize' => 10,
            'defaultSort' => $this->datatable->getDefaultSort(),
            'searchable' => true,
            'sortable' => true,
        ], $this->datatable->getOptions());

        $this->datatable->setOptions(['translationDomain' => 'bar']);
        $this->assertSame([
            'translationDomain' => 'bar',
            'defaultPageSize' => 10,
            'defaultSort' => $this->datatable->getDefaultSort(),
            'searchable' => true,
            'sortable' => true,
        ], $this->datatable->getOptions());

        $this->datatable->addOption('defaultPageSize', 20);
        $this->datatable->addOption('actionColumn', ['label' => 'Actions', 'template' => 'path/to/template.html.twig']);
        $this->datatable->addOption('selectorColumn', ['label' => '', 'template' => '']);
        $this->datatable->addOption('defaultSort', [['field' => 'name2', 'order' => 'asc']]);
        $this->datatable->addOption('translationDomain', 'messages+bar');
        $this->assertSame([
            'translationDomain' => 'messages+bar',
            'defaultPageSize' => 20,
            'defaultSort' => [['field' => 'name2', 'order' => 'asc']],
            'searchable' => true,
            'sortable' => true,
            'actionColumn' => ['label' => 'Actions', 'template' => 'path/to/template.html.twig'],
            'selectorColumn' => ['label' => '', 'template' => ''],
        ], $this->datatable->getOptions());

        $this->assertEquals('messages+bar', $this->datatable->getTranslationDomain());
        $this->assertTrue($this->datatable->isSearchable());
        $this->assertTrue($this->datatable->isSortable());

        $this->datatable->addOption('searchable', false);
        $this->datatable->addOption('sortable', false);

        $this->assertFalse($this->datatable->isSearchable());
        $this->assertFalse($this->datatable->isSortable());
    }

    public function testGlobalOptions(): void
    {
        $this->assertSame(Configuration::DEFAULT_CONFIGURATION, $this->datatable->getGlobalOptions());
        $this->assertTrue($this->datatable->isIconUxMode());
        $this->assertEquals('bi:chevron-double-left', $this->datatable->getIcon('icon_first'));
        $this->assertEquals('bi:chevron-left', $this->datatable->getIcon('icon_previous'));
        $this->assertEquals('bi:chevron-right', $this->datatable->getIcon('icon_next'));
    }
}
