<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DataGridBundle\DataGrid\ColumnType\Files;

use FSi\Component\DataGrid\Column\CellViewInterface;
use FSi\Component\DataGrid\Column\ColumnAbstractType;
use FSi\Component\DataGrid\Column\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Image extends ColumnAbstractType
{
    public function getId(): string
    {
        return 'web_image';
    }

    public function initOptions(OptionsResolver $optionsResolver): void
    {
        parent::initOptions($optionsResolver);

        $optionsResolver
            ->setRequired(['width'])
            ->setDefaults(['height' => null])
            ->setAllowedTypes('width', 'integer')
            ->setAllowedTypes('height', ['null', 'integer'])
        ;
    }

    protected function buildCellView(ColumnInterface $column, CellViewInterface $view, $index, $source): void
    {
        $view->setAttribute('width', $column->getOption('width'));
        $view->setAttribute('height', $column->getOption('height'));
    }
}
