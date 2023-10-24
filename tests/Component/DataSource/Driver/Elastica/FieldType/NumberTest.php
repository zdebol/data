<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Component\DataSource\Driver\Elastica\FieldType;

use Tests\FSi\Component\DataSource\Driver\Elastica\TestBase;

class NumberTest extends TestBase
{
    public function setUp(): void
    {
        $this->dataSource = $this->prepareIndex('datetime_index');
    }

    public function testFilterByEmptyParameter(): void
    {
        $this->dataSource->addField('salary', 'number', ['comparison' => 'eq']);

        $result = $this->filterDataSource(['salary' => '']);
        $this->assertCount(11, $result);

        $result = $this->filterDataSource(['salary' => null]);
        $this->assertCount(11, $result);

        $result = $this->filterDataSource(['salary' => []]);
        $this->assertCount(11, $result);
    }

    public function testFilterByNumberEq(): void
    {
        $this->dataSource->addField('salary', 'number', ['comparison' => 'eq']);
        $result = $this->filterDataSource(['salary' => 222222]);

        $this->assertCount(2, $result);
    }

    public function testFilterByNumberGt(): void
    {
        $this->dataSource->addField('salary', 'number', ['comparison' => 'gt']);
        $result = $this->filterDataSource(['salary' => 111111]);

        $this->assertCount(3, $result);
    }

    public function testFilterByNumberGte(): void
    {
        $this->dataSource->addField('salary', 'number', ['comparison' => 'gte']);
        $result = $this->filterDataSource(['salary' => 222222]);

        $this->assertCount(3, $result);
    }

    public function testFilterByNumberLt(): void
    {
        $this->dataSource->addField('salary', 'number', ['comparison' => 'lt']);
        $result = $this->filterDataSource(['salary' => 345]);

        $this->assertCount(2, $result);
    }

    public function testFilterByNumberLte(): void
    {
        $this->dataSource->addField('salary', 'number', ['comparison' => 'lte']);
        $result = $this->filterDataSource(['salary' => 345]);

        $this->assertCount(3, $result);
    }

    public function testFilterByNumberBetween(): void
    {
        $this->dataSource->addField('salary', 'number', ['comparison' => 'between']);
        $result = $this->filterDataSource(['salary' => ['from' => 123, 'to' => 783]]);

        $this->assertCount(7, $result);

        $result = $this->filterDataSource(['salary' => ['from' => 321, 'to' => null]]);

        $this->assertCount(10, $result);
    }
}
