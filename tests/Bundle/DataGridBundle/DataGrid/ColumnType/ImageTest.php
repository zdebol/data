<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Bundle\DataGridBundle\DataGrid\ColumnType;

use FSi\Bundle\DataGridBundle\DataGrid\ColumnType\Files\Image;
use FSi\Component\DataGrid\ColumnTypeExtension\DefaultColumnOptionsExtension;
use FSi\Component\DataGrid\DataGridInterface;
use FSi\Component\DataGrid\DataMapper\PropertyAccessorMapper;
use FSi\Component\Files\WebFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class ImageTest extends TestCase
{
    private Image $columnType;

    public function testCreation(): void
    {
        $file = $this->createMock(WebFile::class);
        $column = $this->columnType->createColumn(
            $this->createMock(DataGridInterface::class),
            'image',
            ['width' => 200]
        );

        self::assertSame(
            ['image' => $file],
            $this->columnType->createCellView($column, 1, (object) ['image' => $file])->getValue()
        );
    }

    protected function setUp(): void
    {
        $this->columnType = new Image(
            [new DefaultColumnOptionsExtension()],
            new PropertyAccessorMapper(PropertyAccess::createPropertyAccessor())
        );
    }
}
