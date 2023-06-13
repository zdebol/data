<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver\Elastica\FieldType;

use DateTimeInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\Exists;
use Elastica\Query\Range;
use FSi\Component\DataSource\Driver\Elastica\Exception\ElasticaDriverException;
use FSi\Component\DataSource\Field\FieldInterface;
use FSi\Component\DataSource\Field\Type\DateTimeTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTime extends AbstractFieldField implements DateTimeTypeInterface
{
    public function buildQuery(BoolQuery $query, BoolQuery $filter, FieldInterface $field): void
    {
        $data = $field->getParameter();
        if (true === $this->isEmpty($data)) {
            return;
        }

        $fieldPath = $field->getOption('field');

        if ('eq' === $field->getOption('comparison')) {
            $formattedDate = $data->format($this->getFormat());
            $filter->addMust(
                new Range(
                    $fieldPath,
                    ['gte' => $formattedDate, 'lte' => $formattedDate]
                )
            );
        } elseif (true === in_array($field->getOption('comparison'), ['lt', 'lte', 'gt', 'gte'], true)) {
            $filter->addMust(
                new Range(
                    $fieldPath,
                    [$field->getOption('comparison') => $data->format($this->getFormat())]
                )
            );
        } elseif ('between' === $field->getOption('comparison')) {
            if (false === is_array($data)) {
                throw new ElasticaDriverException("'between' comparison needs an array");
            }

            if (null !== ($data['from'] ?? null)) {
                $filter->addMust(
                    new Range(
                        $fieldPath,
                        ['gte' => $data['from']->format($this->getFormat())]
                    )
                );
            }

            if (null !== ($data['to'] ?? null)) {
                $filter->addMust(
                    new Range(
                        $fieldPath,
                        ['lte' => $data['to']->format($this->getFormat())]
                    )
                );
            }
        } elseif ('isNull' === $field->getOption('comparison')) {
            $existsQuery = new Exists($fieldPath);
            if ('null' === $data) {
                $filter->addMustNot($existsQuery);
            } elseif ('no_null' === $data) {
                $filter->addMust($existsQuery);
            }
        } else {
            throw new ElasticaDriverException("Unexpected comparison type \"{$field->getOption('comparison')}\".");
        }
    }

    public function getId(): string
    {
        return 'datetime';
    }

    public function initOptions(OptionsResolver $optionsResolver): void
    {
        parent::initOptions($optionsResolver);

        $optionsResolver->setAllowedValues('comparison', ['eq', 'lt', 'lte', 'gt', 'gte', 'between', 'isNull']);
    }

    protected function getFormat(): string
    {
        return DateTimeInterface::ATOM;
    }
}
