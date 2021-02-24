<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Event\DataSourceEvent;

use FSi\Component\DataSource\DataSourceInterface;
use Symfony\Contracts\EventDispatcher\Event;

class DataSourceEventArgs extends Event
{
    /**
     * @var DataSourceInterface
     */
    private $datasource;

    public function __construct(DataSourceInterface $datasource)
    {
        $this->datasource = $datasource;
    }

    public function getDataSource(): DataSourceInterface
    {
        return $this->datasource;
    }
}
