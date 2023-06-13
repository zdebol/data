<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver\Elastica;

use Elastica\Query;
use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use Elastica\SearchableInterface;
use FSi\Component\DataSource\Driver\AbstractDriver;
use FSi\Component\DataSource\Driver\Elastica\Event\PostGetResult;
use FSi\Component\DataSource\Driver\Elastica\Event\PreGetResult;
use FSi\Component\DataSource\Driver\Elastica\FieldType\FieldTypeInterface;
use FSi\Component\DataSource\Result;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;

use function get_class;
use function sprintf;

/**
 * @template T
 * @template-extends AbstractDriver<T>
 */
class ElasticaDriver extends AbstractDriver
{
    private SearchableInterface $searchable;
    private ?AbstractQuery $userSubQuery;
    private ?AbstractQuery $userFilter;
    private ?Query $masterQuery;

    /**
     * @param array<FieldTypeInterface> $fieldTypes
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        array $fieldTypes,
        SearchableInterface $searchable,
        ?AbstractQuery $userSubQuery = null,
        ?AbstractQuery $userFilter = null,
        ?Query $masterQuery = null
    ) {
        parent::__construct($eventDispatcher, $fieldTypes);

        $this->searchable = $searchable;
        $this->userSubQuery = $userSubQuery;
        $this->userFilter = $userFilter;
        $this->masterQuery = $masterQuery;
    }

    public function getResult(array $fields, ?int $first, ?int $max): Result
    {
        $subQueries = new BoolQuery();
        $filters = new BoolQuery();
        $query = $this->masterQuery ?? new Query();

        $this->getEventDispatcher()->dispatch(new PreGetResult($this, $fields, $query));

        if (null !== $this->userFilter) {
            $filters->addMust($this->userFilter);
        }

        foreach ($fields as $field) {
            $fieldType = $field->getType();
            if (false === $fieldType instanceof FieldTypeInterface) {
                throw new RuntimeException(sprintf(
                    'All fields must be instances of "%s", but got "%s"',
                    FieldTypeInterface::class,
                    get_class($fieldType)
                ));
            }

            $fieldType->buildQuery($subQueries, $filters, $field);
        }

        if (null !== $this->userSubQuery) {
            $subQueries->addMust($this->userSubQuery);
        }

        if (
            true === $subQueries->hasParam('should')
            || true === $subQueries->hasParam('must')
            || true === $subQueries->hasParam('must_not')
        ) {
            $query->setQuery($subQueries);
        }

        $tempFilters = $filters->getParams();
        if (0 !== count($tempFilters)) {
            $query->setPostFilter($filters);
        }

        if ($first !== null) {
            $query->setFrom($first);
        }
        if ($max !== null) {
            $query->setSize($max);
        }

        $result = new ElasticaResult($this->searchable->search(Query::create($query)), $this->searchable);
        $event = new PostGetResult($this, $fields, $result);
        $this->getEventDispatcher()->dispatch($event);

        return $event->getResult();
    }
}
