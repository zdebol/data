<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Component\DataSource;

use FSi\Component\DataSource\DataSource;
use FSi\Component\DataSource\DataSourceFactoryInterface;
use FSi\Component\DataSource\Driver\DriverInterface;
use FSi\Component\DataSource\Event\PostBuildView;
use FSi\Component\DataSource\Event\PostGetParameters;
use FSi\Component\DataSource\Event\PreBindParameters;
use FSi\Component\DataSource\Event\PreBuildView;
use FSi\Component\DataSource\Exception\DataSourceException;
use FSi\Component\DataSource\Field\Event\PreBindParameter;
use FSi\Component\DataSource\Field\FieldInterface;
use FSi\Component\DataSource\Field\Type\FieldTypeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\FSi\Component\DataSource\Fixtures\TestResult;

final class DataSourceTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testDataSourceCreate(): void
    {
        new DataSource(
            'datasource',
            $this->createMock(EventDispatcherInterface::class),
            $this->createDriverMock()
        );
    }

    public function testDataSourceName(): void
    {
        $driver = $this->createDriverMock();

        $dataSource = new DataSource(
            'name1',
            $this->createMock(EventDispatcherInterface::class),
            $driver
        );
        self::assertEquals('name1', $dataSource->getName());

        $dataSource = new DataSource(
            'name2',
            $this->createMock(EventDispatcherInterface::class),
            $driver
        );
        self::assertEquals('name2', $dataSource->getName());
    }

    public function testDataSourceExceptionOnWrongName(): void
    {
        $this->expectException(DataSourceException::class);
        new DataSource(
            'wrong-name',
            $this->createMock(EventDispatcherInterface::class),
            $this->createDriverMock()
        );
    }

    public function testDataSourceCreatingAddingGettingDeletingFields(): void
    {
        $driver = $this->createDriverMock();
        $dataSource = new DataSource(
            'datasource',
            $this->createMock(EventDispatcherInterface::class),
            $driver
        );

        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn('name1');
        $field->method('getDataSourceName')->willReturn('datasource');
        $fieldType = $this->createMock(FieldTypeInterface::class);
        $fieldType->method('createField')->with(
            'datasource',
            self::anything(),
            self::anything()
        )->willReturn($field);

        $driver->method('getFieldType')->with('text')->willReturn($fieldType);

        $dataSource->addField('name1', 'text', ['comparison' => 'comp1']);

        self::assertCount(1, $dataSource->getFields());
        self::assertTrue($dataSource->hasField('name1'));
        self::assertFalse($dataSource->hasField('wrong'));

        $dataSource->clearFields();
        self::assertCount(0, $dataSource->getFields());

        $dataSource->addField('name1', 'text', ['comparison' => 'comp1']);
        self::assertCount(1, $dataSource->getFields());
        self::assertFalse($dataSource->hasField('name'));
        self::assertTrue($dataSource->hasField('name1'));
        self::assertFalse($dataSource->hasField('name2'));

        self::assertEquals($field, $dataSource->getField('name1'));

        $dataSource->removeField('name1');
        self::assertCount(0, $dataSource->getFields());
        $dataSource->removeField('name');

        $this->expectException(DataSourceException::class);
        $dataSource->getField('wrong');
    }

    public function testParametersBindingException(): void
    {
        $dataSource = new DataSource(
            'datasource',
            $this->createMock(EventDispatcherInterface::class),
            $this->createDriverMock()
        );
        $dataSource->bindParameters([]);
        $this->expectException(DataSourceException::class);
        $dataSource->bindParameters('nonarray');
    }

    public function testBindAndGetResult(): void
    {
        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn('field');
        $field->expects(self::exactly(2))->method('bindParameter');

        $fieldType = $this->createMock(FieldTypeInterface::class);
        $fieldType->method('createField')->willReturn($field);

        $driver = $this->createDriverMock();
        $driver->method('getFieldType')->with('type')->willReturn($fieldType);

        $dataSource = new DataSource(
            'datasource',
            $this->createMock(EventDispatcherInterface::class),
            $driver
        );
        $field->method('getDataSourceName')->willReturn('datasource');

        $testResult = new TestResult();

        $firstData = [
            'datasource' => [
                'fields' => [
                    'field' => 'value',
                    'other' => 'notimportant'
                ],
            ],
        ];
        $secondData = [
            'datasource' => [
                'fields' => ['somefield' => 'somevalue'],
            ],
        ];


        $driver->expects(self::once())
            ->method('getResult')
            ->with(['field' => $field])
            ->willReturn($testResult)
        ;

        $dataSource->addField('field', 'type', ['comparison' => 'eq']);
        $dataSource->bindParameters($firstData);
        $dataSource->bindParameters($secondData);

        $dataSource->getResult();
    }

    public function testPaginationParametersForwardingToDriver(): void
    {
        $dataSource = new DataSource(
            'datasource',
            $this->createMock(EventDispatcherInterface::class),
            $this->createDriverMock()
        );

        $dataSource->setMaxResults(20);
        $dataSource->setFirstResult(40);

        self::assertEquals(20, $dataSource->getMaxResults());
        self::assertEquals(40, $dataSource->getFirstResult());
    }

    public function testPreAndPostGetParametersCalls(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $fieldType = $this->createMock(FieldTypeInterface::class);

        $driver = $this->createDriverMock();
        $driver->method('getFieldType')->willReturn($fieldType);

        $dataSource = new DataSource(
            'datasource',
            $eventDispatcher,
            $driver
        );

        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn('key');
        $field->expects(self::never())->method('getParameter');

        $field2 = $this->createMock(FieldInterface::class);
        $field2->method('getName')->willReturn('key2');
        $field2->expects(self::never())->method('getParameter');

        $fieldType->method('createField')
            ->withConsecutive(
                ['datasource', 'field', []],
                ['datasource', 'field2', []]
            )
            ->willReturnOnConsecutiveCalls($field, $field2)
        ;

        $eventDispatcher->expects(self::exactly(4))
            ->method('dispatch')
            ->withConsecutive(
                [self::isInstanceOf(PreBindParameters::class)],
                [self::isInstanceOf(PreBindParameter::class)],
                [self::isInstanceOf(PreBindParameter::class)],
                [self::isInstanceOf(PostGetParameters::class)]
            )
        ;

        $dataSource->addField('field', 'type', []);
        $dataSource->addField('field2', 'type', []);

        $dataSource->bindParameters([
            'datasource' => [
                'fields' => ['key' => 'a', 'key2' => 'b', 'ignoredKey' => 'c']
            ]
        ]);

        self::assertEquals(
            ['datasource' => ['fields' => ['key' => 'a', 'key2' => 'b']]],
            $dataSource->getBoundParameters()
        );
    }

    public function testViewCreation(): void
    {
        $driver = $this->createDriverMock();

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [self::isInstanceOf(PreBuildView::class)],
                [self::isInstanceOf(PostGetParameters::class)],
                [self::isInstanceOf(PostBuildView::class)]
            );

        $dataSource = new DataSource(
            'datasource',
            $eventDispatcher,
            $driver
        );
        $view = $dataSource->createView();
        self::assertEquals('datasource', $view->getName());
    }

    public function testExtensionsCallsDuringBindParameters(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $driver = $this->createDriverMock();

        $dataSource = new DataSource(
            'datasource',
            $eventDispatcher,
            $driver
        );

        $testResult = new TestResult();
        $driver->method('getResult')->willReturn($testResult);

        $dataSource->addField('field', 'text', ['comparison' => 'eq']);

        $eventDispatcher->expects(self::exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [self::isInstanceOf(PreBindParameters::class)],
                [self::isInstanceOf(PreBindParameter::class)]
            )
        ;

        $dataSource->bindParameters(['datasource' => []]);
    }

    /**
     * @return DriverInterface<mixed>&MockObject
     */
    private function createDriverMock(): MockObject
    {
        return $this->createMock(DriverInterface::class);
    }
}
