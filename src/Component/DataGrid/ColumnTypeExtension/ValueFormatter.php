<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataGrid\ColumnTypeExtension;

use InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function count;
use function current;
use function implode;
use function is_array;
use function is_callable;
use function is_string;
use function reset;
use function sprintf;
use function vsprintf;

final class ValueFormatter
{
    /**
     * @param mixed $initialValue
     * @param string|callable|null $format
     * @param mixed $emptyValue
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function format(
        $initialValue,
        ?string $glue,
        $format,
        $emptyValue
    ) {
        return $this->formatValue(
            $this->populateValue($initialValue, $emptyValue),
            $format,
            $glue
        );
    }

    /**
     * @param mixed $value
     * @param mixed $emptyValue
     * @return mixed
     */
    private function populateValue($value, $emptyValue)
    {
        if (true === is_string($emptyValue)) {
            if (true === $this->isEmpty($value)) {
                return $emptyValue;
            }

            if (false === is_array($value)) {
                return $value;
            }

            return array_map(
                fn($item) => false === $this->isEmpty($item) ? $item : $emptyValue,
                $value
            );
        }

        /**
         * If $value is simple string and $empty_value is an array, then there is
         * no way to guess which empty_value should be used.
         */
        if (true === is_string($value)) {
            return $value;
        }

        if (false === is_array($value)) {
            return $value;
        }

        $formattedValues = [];
        foreach ($value as $field => $fieldValue) {
            $formattedValues[] = false === $this->isEmpty($fieldValue)
                ? $fieldValue
                : $emptyValue[$field] ?? ''
            ;
        }

        return $formattedValues;
    }

    /**
     * @param mixed $value
     * @param string|callable|null $format
     * @param string|null $glue
     * @return mixed
     */
    private function formatValue($value, $format = null, ?string $glue = null)
    {
        if (true === is_array($value) && null !== $glue && null === $format) {
            $value = implode($glue, $value);
        }

        if (null !== $format) {
            if (true === is_array($value)) {
                if (null !== $glue) {
                    $renderedValues = [];
                    foreach ($value as $val) {
                        $renderedValues[] = $this->formatSingleValue($val, $format);
                    }

                    $value = implode($glue, $renderedValues);
                } else {
                    $value = $this->formatMultipleValues($value, $format);
                }
            } else {
                $value = $this->formatSingleValue($value, $format);
            }
        }

        if (true === is_array($value) && 1 === count($value)) {
            reset($value);
            $value = current($value);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param string|callable $template
     * @return string
     */
    private function formatSingleValue($value, $template): string
    {
        if (true === is_callable($template)) {
            return $template($value);
        }

        return sprintf($template, $value);
    }

    /**
     * @param array<string,mixed> $value
     * @param string|callable $template
     * @return string
     */
    private function formatMultipleValues(array $value, $template): string
    {
        if (true === is_callable($template)) {
            return $template($value);
        }

        return vsprintf($template, $value);
    }

    /**
     * @param mixed $value
     */
    private function isEmpty($value): bool
    {
        return '' === $value || null === $value;
    }
}
