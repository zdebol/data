<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataGrid\ColumnTypeExtension;

use FSi\Component\DataGrid\Column\ColumnAbstractTypeExtension;
use FSi\Component\DataGrid\Column\ColumnInterface;
use FSi\Component\DataGrid\ColumnType\Boolean;
use FSi\Component\DataGrid\ColumnType\Collection;
use FSi\Component\DataGrid\ColumnType\DateTime;
use FSi\Component\DataGrid\ColumnType\Money;
use FSi\Component\DataGrid\ColumnType\Number;
use FSi\Component\DataGrid\ColumnType\Text;
use InvalidArgumentException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function count;

class ValueFormatColumnOptionsExtension extends ColumnAbstractTypeExtension
{
    private ValueFormatter $valueFormatter;

    public function __construct(ValueFormatter $valueFormatter)
    {
        $this->valueFormatter = $valueFormatter;
    }

    public static function getExtendedColumnTypes(): array
    {
        return [
            Text::class,
            Boolean::class,
            DateTime::class,
            Collection::class,
            Number::class,
            Money::class,
        ];
    }

    public function filterValue(ColumnInterface $column, $value)
    {
        /** @var string|null $glue */
        $glue = $column->getOption('value_glue');
        /** @var string|callable|null $format */
        $format = $column->getOption('value_format');
        /** @var string $emptyValue */
        $emptyValue = $column->getOption('empty_value');

        return $this->valueFormatter->format($value, $glue, $format, $emptyValue);
    }

    public function initOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'value_glue' => null,
            'value_format' => null,
            'empty_value' => '',
        ]);

        $optionsResolver->setAllowedTypes('value_glue', ['string', 'null']);
        $optionsResolver->setAllowedTypes('value_format', ['string', 'callable', 'null']);
        $optionsResolver->setAllowedTypes('empty_value', 'string');

        $optionsResolver->setNormalizer(
            'value_glue',
            function (Options $options, ?string $glue): ?string {
                /** @var list<string> $fieldMapping */
                $fieldMapping = $options['field_mapping'];

                /** @var string|callable|null $format */
                $format = $options['value_format'];

                // Introduce validation instead of normalization after Symfony 6.x
                if (null === $glue && null === $format && 1 < count($fieldMapping)) {
                    throw new InvalidArgumentException(
                        'At least one of "value_format" or "value_glue" option is missing.'
                    );
                }

                return $glue;
            }
        );
    }
}
