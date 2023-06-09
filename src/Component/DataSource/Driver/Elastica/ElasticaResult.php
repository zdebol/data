<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver\Elastica;

use ArrayIterator;
use Countable;
use Elastica\SearchableInterface;
use FSi\Component\DataSource\Result;
use Iterator;
use Elastica\ResultSet;

/**
 * @template T
 * @implements Result<T>
 */
class ElasticaResult implements Countable, Result
{
    private ResultSet $resultSet;
    private SearchableInterface $searchable;

    public function __construct(ResultSet $resultSet, SearchableInterface $searchable)
    {
        $this->resultSet = $resultSet;
        $this->searchable = $searchable;
    }

    public function count(): int
    {
        return $this->resultSet->getTotalHits();
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->resultSet->getResults());
    }

    public function hasAggregations(): bool
    {
        return $this->resultSet->hasAggregations();
    }

    public function getSearchable(): SearchableInterface
    {
        return $this->searchable;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getAggregations(): array
    {
        return $this->resultSet->getAggregations();
    }

    /**
     * @param string $name
     * @return array<string, mixed>
     */
    public function getAggregation(string $name): array
    {
        return $this->resultSet->getAggregation($name);
    }
}
