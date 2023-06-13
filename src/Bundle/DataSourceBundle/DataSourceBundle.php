<?php

/**
 * (c) FSi Sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DataSourceBundle;

use FSi\Bundle\DataSourceBundle\DependencyInjection\Compiler\FOSElasticaPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use FSi\Bundle\DataSourceBundle\DependencyInjection\Compiler\DataSourcePass;
use FSi\Bundle\DataSourceBundle\DependencyInjection\FSIDataSourceExtension;

final class DataSourceBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DataSourcePass());
        $container->addCompilerPass(new FOSElasticaPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 200);
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new FSIDataSourceExtension();
        }

        return $this->extension;
    }
}
