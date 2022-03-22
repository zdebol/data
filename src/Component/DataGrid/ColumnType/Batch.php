<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataGrid\ColumnType;

use FSi\Component\DataGrid\Column\CellViewInterface;
use FSi\Component\DataGrid\Column\ColumnAbstractType;
use FSi\Component\DataGrid\Column\ColumnInterface;

class Batch extends ColumnAbstractType
{
    public function getId(): string
    {
        return 'batch';
    }

    protected function getValue(ColumnInterface $column, $object)
    {
        return null;
    }

    /**
     * @param ColumnInterface $column
     * @param CellViewInterface $view
     * @param int|string $index
     * @param array<string,mixed>|object $source
     */
    protected function buildCellView(ColumnInterface $column, CellViewInterface $view, $index, $source): void
    {
        $view->setAttribute('index', $index);
    }
}
