<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Bundle\DataGridBundle\DataGrid\ColumnTypeExtension;

use FSi\Bundle\DataGridBundle\DataGrid\CellFormBuilder\BooleanCellFormBuilder;
use FSi\Bundle\DataGridBundle\DataGrid\ColumnTypeExtension\BooleanColumnExtension;
use FSi\Bundle\DataGridBundle\DataGrid\ColumnTypeExtension\FormExtension;
use FSi\Component\DataGrid\ColumnType\Boolean;
use FSi\Component\DataGrid\DataGridInterface;
use FSi\Component\DataGrid\DataMapper\DataMapperInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SEEC\PhpUnit\Helper\ConsecutiveParams;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BooleanColumnExtensionTest extends TestCase
{
    use ConsecutiveParams;

    public function testColumnOptions(): void
    {
        $dataMapper = $this->createMock(DataMapperInterface::class);
        $columnType = new Boolean(
            [
                new FormExtension(
                    [new BooleanCellFormBuilder()],
                    $this->createMock(FormFactoryInterface::class),
                    $dataMapper,
                    true
                ),
                new BooleanColumnExtension($this->getTranslator()),
            ],
            $dataMapper
        );
        $column = $columnType->createColumn($this->createMock(DataGridInterface::class), 'grid', []);

        $this->assertEquals('YES', $column->getOption('true_value'));
        $this->assertEquals('NO', $column->getOption('false_value'));
    }

    /**
     * @return TranslatorInterface&MockObject
     */
    private function getTranslator(): MockObject
    {
        /** @var TranslatorInterface&MockObject $translator */
        $translator = $this->createMock(TranslatorInterface::class);

        $translator->expects(self::exactly(2))
            ->method('trans')
            ->with(...self::withConsecutive(
                ['datagrid.boolean.yes', [], 'DataGridBundle'],
                ['datagrid.boolean.no', [], 'DataGridBundle']
            ))->willReturnOnConsecutiveCalls('YES', 'NO');

        return $translator;
    }
}
