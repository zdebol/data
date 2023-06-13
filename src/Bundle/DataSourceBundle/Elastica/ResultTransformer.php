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
use Elastica\SearchableInterface;
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
    private ?array $indexMap = null;
    private TransformerManager $transformerManager;
    private ManagerRegistry $managerRegistry;
    private ConfigManager $configManager;

    public function __construct(
        ConfigManager $configManager,
        TransformerManager $transformerManager,
        ManagerRegistry $managerRegistry
    ) {
        $this->configManager = $configManager;
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

        $indexName = $this->getIndexName($result->getSearchable());
        if (null === $indexName) {
            return;
        }

        $transformer = $this->transformerManager->getTransformer($indexName);
        if (null === $transformer) {
            return;
        }

        $dataClass = $transformer->getObjectClass();
        /** @var array<string, T> $entities */
        $entities = $transformer->transform(iterator_to_array($result->getIterator()));
        $dataIndexer = new DoctrineDataIndexer($this->managerRegistry, $dataClass);
        $transformedResult = [];
        foreach ($entities as $entity) {
            $indexName = $dataIndexer->getIndex($entity);
            $transformedResult[$indexName] = $entity;
        }

        $event->setResult(
            new TransformedElasticaResult($result->count(), $transformedResult, $result->getAggregations())
        );
    }

    private function getIndexName(SearchableInterface $searchable): ?string
    {
        if (false === $searchable instanceof Index) {
            return null;
        }

        if (null === $this->indexMap) {
            $indexNames = $this->configManager->getIndexNames();
            array_walk($indexNames, function (string $indexName): void {
                $this->indexMap[$this->configManager->getIndexConfiguration($indexName)->getElasticSearchName()]
                    = $indexName;
            });
        }

        return $this->indexMap[$searchable->getOriginalName()] ?? null;
    }
}
