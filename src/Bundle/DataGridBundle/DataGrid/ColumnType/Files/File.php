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

final class File extends ColumnAbstractType
{
    public function getId(): string
    {
        return 'web_file';
    }

    public function initOptions(OptionsResolver $optionsResolver): void
    {
        parent::initOptions($optionsResolver);

        $optionsResolver->setDefault('resolve_file_url', true);
        $optionsResolver->setAllowedTypes('resolve_file_url', ['bool']);
    }

    protected function buildCellView(ColumnInterface $column, CellViewInterface $view, $index, $source): void
    {
        $view->setAttribute('resolve_file_url', $column->getOption('resolve_file_url'));
    }
}
