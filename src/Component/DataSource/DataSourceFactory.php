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

use function array_key_exists;

final class DataSourceFactory implements DataSourceFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;
    /**
     * @var DriverFactoryManagerInterface<mixed>
     */
    private DriverFactoryManagerInterface $driverFactoryManager;
    /**
     * @var array<DataSourceInterface<mixed>>
     */
    private array $dataSources;

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
        $this->dataSources = [];
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
        $dataSource = new DataSource($name, $this, $this->eventDispatcher, $driver);
        $this->dataSources[$name] = $dataSource;

        return $dataSource;
    }

    public function getAllParameters(): array
    {
        $result = [];
        foreach ($this->dataSources as $dataSource) {
            $result[] = $dataSource->getParameters();
        }

        if (0 !== count($result)) {
            $result = array_merge(...$result);
        }

        return $result;
    }

    /**
     * @param DataSourceInterface<object> $except
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function getOtherParameters(DataSourceInterface $except): array
    {
        $result = [];
        foreach ($this->dataSources as $dataSource) {
            if ($dataSource !== $except) {
                $result[] = $dataSource->getParameters();
            }
        }

        if (0 !== count($result)) {
            $result = array_merge_recursive(...$result);
        }

        return $result;
    }

    private function checkDataSourceName(string $name): void
    {
        if ('' === $name) {
            throw new DataSourceException('Name of data source cannot be empty.');
        }

        if (true === array_key_exists($name, $this->dataSources)) {
            throw new DataSourceException("Name of data source \"{$name}\" must be unique.");
        }

        if (1 !== preg_match('/^[\w]+$/', $name)) {
            throw new DataSourceException(
                "Name of data source \"{$name}\" may contain only word characters and digits."
            );
        }
    }
}
