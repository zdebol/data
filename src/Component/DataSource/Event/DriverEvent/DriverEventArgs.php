<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Event\DriverEvent;

use FSi\Component\DataSource\Driver\DriverInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event class for Driver.
 */
class DriverEventArgs extends Event
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var array
     */
    private $fields;

    /**
     * @param DriverInterface $driver
     * @param array $fields
     */
    public function __construct(DriverInterface $driver, array $fields)
    {
        $this->driver = $driver;
        $this->fields = $fields;
    }

    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
