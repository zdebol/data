<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DataGridBundle\Twig\Extension\Files;

use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class FilesDummyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('file_url', [$this, 'throwMissingExtensionException']),
            new TwigFilter('file_name', [$this, 'throwMissingExtensionException'])
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_web_file', [$this, 'throwMissingExtensionException'])
        ];
    }

    public function throwMissingExtensionException(): void
    {
        throw new RuntimeException('"fsi/files" is not registered in the kernel.');
    }
}
