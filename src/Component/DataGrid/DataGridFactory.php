<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataGrid;

use FSi\Component\DataGrid\Column\ColumnTypeInterface;
use FSi\Component\DataGrid\Exception\DataGridException;
use FSi\Component\DataGrid\Exception\UnexpectedTypeException;
use Psr\EventDispatcher\EventDispatcherInterface;

use function array_key_exists;

final class DataGridFactory implements DataGridFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;
    /**
     * @var array<array-key,string>
     */
    private array $dataGridNames;
    /**
     * @var array<ColumnTypeInterface>
     */
    private array $columnTypes;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param iterable<ColumnTypeInterface> $columnTypes
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        iterable $columnTypes
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->dataGridNames = [];
        $this->columnTypes = [];
        foreach ($columnTypes as $columnType) {
            $this->columnTypes[$columnType->getId()] = $columnType;
        }
    }

    public function hasColumnType(string $type): bool
    {
        return array_key_exists($type, $this->columnTypes);
    }

    public function getColumnType(string $type): ColumnTypeInterface
    {
        if (false === $this->hasColumnType($type)) {
            throw new UnexpectedTypeException("Unsupported column type \"{$type}\".");
        }

        return $this->columnTypes[$type];
    }

    public function createDataGrid(string $name): DataGridInterface
    {
        if (true === in_array($name, $this->dataGridNames, true)) {
            throw new DataGridException("Datagrid name \"{$name}\" is not unique.");
        }

        $this->dataGridNames[] = $name;
        return new DataGrid($this, $this->eventDispatcher, $name);
    }
}
