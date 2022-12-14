<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver\Event;

use FSi\Component\DataSource\Driver\DriverInterface;
use FSi\Component\DataSource\Field\FieldInterface;

/**
 * @template T
 */
abstract class DriverEventArgs
{
    /**
     * @var DriverInterface<T>
     */
    private DriverInterface $driver;
    /**
     * @var array<FieldInterface>
     */
    private array $fields;

    /**
     * @param DriverInterface<T> $driver
     * @param array<FieldInterface> $fields
     */
    public function __construct(DriverInterface $driver, array $fields)
    {
        $this->driver = $driver;
        $this->fields = $fields;
    }

    /**
     * @return DriverInterface<T>
     */
    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * @return array<FieldInterface>
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
