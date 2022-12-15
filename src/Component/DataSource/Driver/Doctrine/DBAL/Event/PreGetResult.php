<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver\Doctrine\DBAL\Event;

use Doctrine\DBAL\Query\QueryBuilder;
use FSi\Component\DataSource\Driver\DriverInterface;
use FSi\Component\DataSource\Driver\Event\DriverEventArgs;
use FSi\Component\DataSource\Field\FieldInterface;

/**
 * @template T
 * @template-extends DriverEventArgs<T>
 */
class PreGetResult extends DriverEventArgs
{
    private QueryBuilder $queryBuilder;

    /**
     * @param DriverInterface<T> $driver
     * @param array<FieldInterface> $fields
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(DriverInterface $driver, array $fields, QueryBuilder $queryBuilder)
    {
        parent::__construct($driver, $fields);

        $this->queryBuilder = $queryBuilder;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }
}
