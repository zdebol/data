<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Bundle\DataGridBundle\DataGrid\ColumnType;

use FSi\Bundle\DataGridBundle\DataGrid\ColumnType\Files\File;
use FSi\Component\DataGrid\ColumnTypeExtension\DefaultColumnOptionsExtension;
use FSi\Component\DataGrid\DataGridInterface;
use FSi\Component\DataGrid\DataMapper\PropertyAccessorMapper;
use FSi\Component\Files\WebFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class FileTest extends TestCase
{
    private File $columnType;

    public function testCreation(): void
    {
        $file = $this->createMock(WebFile::class);
        $column = $this->columnType->createColumn(
            $this->createMock(DataGridInterface::class),
            'file',
            ['resolve_file_url' => true]
        );

        self::assertSame(
            ['file' => $file],
            $this->columnType->createCellView($column, 1, (object) ['file' => $file])->getValue()
        );
    }

    protected function setUp(): void
    {
        $this->columnType = new File(
            [new DefaultColumnOptionsExtension()],
            new PropertyAccessorMapper(PropertyAccess::createPropertyAccessor())
        );
    }
}
