<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Datatables;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableResponse;

class DatatableResponseTest extends TestCase
{
    public function testToArrayReturnsCorrectStructure(): void
    {
        $total = 100;
        $filtered = 50;
        $data = [
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ];
        $pagination = [
            'current' => 1,
            'hasPrevious' => false,
            'nbPages' => 10,
            'previous' => 0,
            'pages' => [1, 2, 3, 4, 5],
            'pageSize' => 10,
            'hasNext' => true,
            'next' => 2,
        ];

        $response = new DatatableResponse(
            total: $total,
            filtered: $filtered,
            data: $data,
            pagination: $pagination
        );

        $result = $response->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('filtered', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);

        $this->assertSame($total, $result['total']);
        $this->assertSame($filtered, $result['filtered']);
        $this->assertSame($data, $result['data']);
        $this->assertSame($pagination, $result['pagination']);
    }

    public function testToArrayHandlesEmptyData(): void
    {
        $response = new DatatableResponse(
            total: 0,
            filtered: 0,
            data: [],
            pagination: []
        );

        $result = $response->toArray();

        $this->assertIsArray($result);
        $this->assertSame(0, $result['total']);
        $this->assertSame(0, $result['filtered']);
        $this->assertEmpty($result['data']);
        $this->assertEmpty($result['pagination']);
    }

    public function testToArrayHandlesPartialData(): void
    {
        $response = new DatatableResponse(
            total: 10,
            filtered: 5,
            data: [],
            pagination: ['current' => 1, 'nbPages' => 2]
        );

        $result = $response->toArray();

        $this->assertIsArray($result);
        $this->assertSame(10, $result['total']);
        $this->assertSame(5, $result['filtered']);
        $this->assertEmpty($result['data']);
        $this->assertArrayHasKey('current', $result['pagination']);
        $this->assertArrayHasKey('nbPages', $result['pagination']);
    }
}
