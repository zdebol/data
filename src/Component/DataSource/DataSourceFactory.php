<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource;

use FSi\Component\DataSource\Driver\DriverFactoryManagerInterface;
use FSi\Component\DataSource\Exception\DataSourceException;
use Psr\EventDispatcher\EventDispatcherInterface;

use function in_array;

final class DataSourceFactory implements DataSourceFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;
    /**
     * @var DriverFactoryManagerInterface<mixed>
     */
    private DriverFactoryManagerInterface $driverFactoryManager;
    /**
     * @var array<array-key,string>
     */
    private array $dataSourceNames;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param DriverFactoryManagerInterface<mixed> $driverFactoryManager
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DriverFactoryManagerInterface $driverFactoryManager
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->driverFactoryManager = $driverFactoryManager;
        $this->dataSourceNames = [];
    }

    /**
     * @param string $driverName
     * @param array<string,mixed> $driverOptions
     * @param string $name
     * @return DataSourceInterface<mixed>
     */
    public function createDataSource(
        string $driverName,
        array $driverOptions = [],
        string $name = 'datasource'
    ): DataSourceInterface {
        $this->checkDataSourceName($name);

        $driver = $this->driverFactoryManager->getFactory($driverName)->createDriver($driverOptions);
        $dataSource = new DataSource($name, $this->eventDispatcher, $driver);
        $this->dataSourceNames[] = $name;

        return $dataSource;
    }

    private function checkDataSourceName(string $name): void
    {
        if ('' === $name) {
            throw new DataSourceException('Name of data source cannot be empty.');
        }

        if (true === in_array($name, $this->dataSourceNames, true)) {
            throw new DataSourceException("Name of data source \"{$name}\" must be unique.");
        }

        if (1 !== preg_match('/^[\w]+$/', $name)) {
            throw new DataSourceException(
                "Name of data source \"{$name}\" may contain only word characters and digits."
            );
        }
    }
}
