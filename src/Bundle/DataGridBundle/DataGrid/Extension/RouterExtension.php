<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DataGridBundle\DataGrid\Extension;

use FSi\Bundle\DataGridBundle\DataGrid\ColumnType\Action;
use FSi\Component\DataGrid\ColumnTypeExtension\DefaultColumnOptionsExtension;
use FSi\Component\DataGrid\DataGridAbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RouterExtension extends DataGridAbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    protected function loadColumnTypes(): array
    {
        return [
            new Action($this->urlGenerator, $this->requestStack, [new DefaultColumnOptionsExtension()]),
        ];
    }
}