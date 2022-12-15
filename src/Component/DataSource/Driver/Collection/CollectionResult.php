<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use FSi\Component\DataSource\Result;
use ReturnTypeWillChange;

/**
 * @template T
 * @template-implements Result<T>
 * @template-implements ArrayAccess<int|string,T>
 */
class CollectionResult implements ArrayAccess, Countable, Result
{
    /**
     * @var Collection<int|string,T>
     */
    private Collection $collection;
    private int $count;

    /**
     * @param Selectable<int|string,T> $collection
     * @param Criteria $criteria
     */
    public function __construct(Selectable $collection, Criteria $criteria)
    {
        $this->collection = $collection->matching($criteria);

        $countCriteria = clone $criteria;
        $countCriteria->setFirstResult(null);
        $countCriteria->setMaxResults(null);
        $this->count = $collection->matching($countCriteria)->count();
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return ArrayIterator<int|string,T>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection->toArray());
    }

    public function offsetExists($offset): bool
    {
        return $this->collection->containsKey($offset);
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->collection->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->collection->add($value);
            return;
        }

        $this->collection->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->collection->remove($offset);
    }
}
