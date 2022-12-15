<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Event;

use FSi\Component\DataSource\DataSourceInterface;

abstract class DataSourceEventArgs
{
    /**
     * @var DataSourceInterface<mixed>
     */
    private DataSourceInterface $dataSource;

    /**
     * @param DataSourceInterface<mixed> $dataSource
     */
    public function __construct(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return DataSourceInterface<mixed>
     */
    public function getDataSource(): DataSourceInterface
    {
        return $this->dataSource;
    }
}
