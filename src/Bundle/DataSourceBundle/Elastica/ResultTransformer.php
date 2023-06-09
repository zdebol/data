<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DataSourceBundle\Elastica;

use Doctrine\Persistence\ManagerRegistry;
use FOS\ElasticaBundle\Configuration\ConfigManager;
use FOS\ElasticaBundle\Elastica\Index;
use FSi\Component\DataIndexer\DoctrineDataIndexer;
use FSi\Component\DataSource\DataSourceEventSubscriberInterface;
use FSi\Component\DataSource\Driver\Elastica\ElasticaResult;
use FSi\Component\DataSource\Driver\Elastica\Event\PostGetResult;

use function array_walk;
use function iterator_to_array;

final class ResultTransformer implements DataSourceEventSubscriberInterface
{
    /**
     * @var array<string, string>
     */
    private array $indexMap = [];
    private TransformerManager $transformerManager;
    private ManagerRegistry $managerRegistry;

    public function __construct(
        ConfigManager $configManager,
        TransformerManager $transformerManager,
        ManagerRegistry $managerRegistry
    ) {
        $indexNames = $configManager->getIndexNames();
        array_walk($indexNames, function (string $indexName) use ($configManager): void {
            $this->indexMap[$configManager->getIndexConfiguration($indexName)->getElasticSearchName()] = $indexName;
        });
        $this->transformerManager = $transformerManager;
        $this->managerRegistry = $managerRegistry;
    }

    public static function getPriority(): int
    {
        return 1024;
    }

    /**
     * @template T
     * @param PostGetResult<T> $event
     */
    public function __invoke(PostGetResult $event): void
    {
        $result = $event->getResult();
        if (false === $result instanceof ElasticaResult) {
            return;
        }

        $searchable = $result->getSearchable();
        if (false === $searchable instanceof Index) {
            return;
        }

        $index = $this->indexMap[$searchable->getOriginalName()] ?? null;
        if (null === $index) {
            return;
        }

        $transformer = $this->transformerManager->getTransformer($index);
        if (null === $transformer) {
            return;
        }

        $dataClass = $transformer->getObjectClass();
        /** @var array<string, T> $entities */
        $entities = $transformer->transform(iterator_to_array($result->getIterator()));
        $dataIndexer = new DoctrineDataIndexer($this->managerRegistry, $dataClass);
        $transformedResult = [];
        foreach ($entities as $entity) {
            $index = $dataIndexer->getIndex($entity);
            $transformedResult[$index] = $entity;
        }

        $event->setResult(
            new TransformedElasticaResult($result->count(), $transformedResult, $result->getAggregations())
        );
    }
}
