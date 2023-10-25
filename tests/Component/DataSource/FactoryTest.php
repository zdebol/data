<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Component\DataSource;

use FSi\Component\DataSource\DataSourceFactory;
use FSi\Component\DataSource\Driver\Collection\CollectionFactory;
use FSi\Component\DataSource\Driver\DriverFactoryManager;
use FSi\Component\DataSource\Exception\DataSourceException;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

final class FactoryTest extends TestCase
{
    public function testFactoryExceptionOnUnknownDriver(): void
    {
        $this->expectException(DataSourceException::class);
        $this->expectExceptionMessage('Driver "unknownDriver" does not exist.');

        $factory = new DataSourceFactory(
            $this->createMock(EventDispatcherInterface::class),
            new DriverFactoryManager([])
        );
        $factory->createDataSource('unknownDriver');
    }

    public function testFactoryExceptionOnCreatingADataSourceWithNonUniqueName(): void
    {
        $this->expectException(DataSourceException::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $driveFactoryManager = new DriverFactoryManager([new CollectionFactory($eventDispatcher, [])]);
        $factory = new DataSourceFactory($eventDispatcher, $driveFactoryManager);

        $factory->createDataSource('collection', ['collection' => []], 'unique');
        $factory->createDataSource('collection', ['collection' => []], 'nonunique');
        $factory->createDataSource('collection', ['collection' => []], 'nonunique');
    }

    public function testFactoryExceptionOnCreatingADataSourceWithIncorrectName(): void
    {
        $this->expectException(DataSourceException::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $driveFactoryManager = new DriverFactoryManager([new CollectionFactory($eventDispatcher, [])]);
        $factory = new DataSourceFactory($eventDispatcher, $driveFactoryManager);

        $factory->createDataSource('collection', ['collection' => []], 'wrong-one');
    }

    public function testFactoryExceptionOnCreatingADataSourceWithEmptyName(): void
    {
        $this->expectException(DataSourceException::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $driveFactoryManager = new DriverFactoryManager([new CollectionFactory($eventDispatcher, [])]);
        $factory = new DataSourceFactory($eventDispatcher, $driveFactoryManager);

        $factory->createDataSource('collection', ['collection' => []], '');
    }
}
