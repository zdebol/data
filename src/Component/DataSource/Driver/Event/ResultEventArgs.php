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
use FSi\Component\DataSource\Result;

/**
 * @template T
 * @template-extends DriverEventArgs<T>
 */
abstract class ResultEventArgs extends DriverEventArgs
{
    /**
     * @var Result<T>
     */
    private Result $result;

    /**
     * @param DriverInterface<T> $driver
     * @param array<FieldInterface> $fields
     * @param Result<T> $result
     */
    public function __construct(DriverInterface $driver, array $fields, Result $result)
    {
        parent::__construct($driver, $fields);

        $this->setResult($result);
    }

    /**
     * @param Result<T> $result
     */
    public function setResult(Result $result): void
    {
        $this->result = $result;
    }

    /**
     * @return Result<T>
     */
    public function getResult(): Result
    {
        return $this->result;
    }
}
