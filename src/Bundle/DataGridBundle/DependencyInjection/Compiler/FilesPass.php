<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DataGridBundle\DependencyInjection\Compiler;

use FSi\Bundle\DataGridBundle\Twig\Extension\Files\FilesDummyExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class FilesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (true === $container->hasExtension('fsi_files')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
            $loader->load('files.xml');
        } else {
            $definition = new Definition(FilesDummyExtension::class);
            $definition->addTag('twig.extension');

            $container->addDefinitions([FilesDummyExtension::class => $definition]);
        }
    }
}
