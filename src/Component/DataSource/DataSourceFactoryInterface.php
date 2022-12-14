<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource;

interface DataSourceFactoryInterface
{
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
    ): DataSourceInterface;

    /**
     * Return array of all parameters from all dataSources.
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function getAllParameters(): array;

    /**
     * Return array of all parameters form all dataSources except given.
     *
     * @param DataSourceInterface<mixed> $except
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function getOtherParameters(DataSourceInterface $except): array;
}
