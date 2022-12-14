<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver\Collection\Event;

use Doctrine\Common\Collections\Criteria;
use FSi\Component\DataSource\Driver\DriverInterface;
use FSi\Component\DataSource\Driver\Event\DriverEventArgs;
use FSi\Component\DataSource\Field\FieldInterface;

/**
 * @template T
 * @template-extends DriverEventArgs<T>
 */
class PreGetResult extends DriverEventArgs
{
    private Criteria $criteria;

    /**
     * @param DriverInterface<T> $driver
     * @param array<FieldInterface> $fields
     * @param Criteria $queryBuilder
     */
    public function __construct(DriverInterface $driver, array $fields, Criteria $queryBuilder)
    {
        parent::__construct($driver, $fields);

        $this->criteria = $queryBuilder;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }
}
