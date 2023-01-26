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
}
