<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataGrid\ColumnTypeExtension;

use function array_map;
use function count;
use function current;
use function implode;
use function is_array;
use function is_callable;
use function reset;
use function sprintf;
use function vsprintf;

final class ValueFormatter
{
    /**
     * @param mixed $initialValue
     * @param string|callable|null $format
     * @return mixed
     */
    public function format(
        $initialValue,
        ?string $glue,
        $format,
        string $emptyValue
    ) {
        return $this->formatValue(
            $this->populateValue($initialValue, $emptyValue),
            $format,
            $glue
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function populateValue($value, string $emptyValue)
    {
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
     * @param mixed $value
     * @param string|callable|null $format
     * @param string|null $glue
     * @return mixed
     */
    private function formatValue($value, $format, ?string $glue)
    {
        if (true === is_array($value) && null !== $glue && null === $format) {
            $value = implode($glue, $value);
        }

        if (null !== $format) {
            $value = true === is_array($value)
                ? $this->formatArrayValue($value, $format, $glue)
                : $this->formatValueByTemplate($value, $format)
            ;
        }

        if (true === is_array($value) && 1 === count($value)) {
            reset($value);
            $value = current($value);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $value
     * @param string|callable $format
     */
    private function formatArrayValue(array $value, $format, ?string $glue): string
    {
        if (null === $glue) {
            return $this->formatValueByTemplate($value, $format);
        }

        $formattedValues = [];
        foreach ($value as $val) {
            $formattedValues[] = $this->formatValueByTemplate($val, $format);
        }

        return implode($glue, $formattedValues);
    }

    /**
     * @param mixed $value
     * @param string|callable $template
     * @return string
     */
    private function formatValueByTemplate($value, $template): string
    {
        if (true === is_callable($template)) {
            return $template($value);
        }

        return true === is_array($value) ? vsprintf($template, $value) : sprintf($template, $value);
    }

    /**
     * @param mixed $value
     */
    private function isEmpty($value): bool
    {
        return '' === $value || null === $value;
    }
}
