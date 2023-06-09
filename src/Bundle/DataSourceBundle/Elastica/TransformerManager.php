<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DataSourceBundle\Elastica;

use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;
use FSi\Component\DataSource\Driver\Elastica\Exception\ElasticaDriverException;

use function array_key_exists;

final class TransformerManager
{
    /**
     * @var array<string, ElasticaToModelTransformerInterface>
     */
    private array $transformers = [];

    /**
     * @param iterable<string, ElasticaToModelTransformerInterface> $transformers
     */
    public function __construct(iterable $transformers)
    {
        foreach ($transformers as $index => $transformer) {
            $this->transformers[$index] = $transformer;
        }
    }

    public function getTransformer(string $index): ?ElasticaToModelTransformerInterface
    {
        return $this->transformers[$index] ?? null;
    }
}
