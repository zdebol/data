<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Extension\Attributes\EventSubscriber;

use FSi\Component\DataSource\DataSourceEventSubscriberInterface;
use FSi\Component\DataSource\Event\PostBuildView;

final class AttributesPostBuildView implements DataSourceEventSubscriberInterface
{
    public static function getPriority(): int
    {
        return 0;
    }

    public function __invoke(PostBuildView $event): void
    {
        $view = $event->getView();

        $view->setAttribute('container_attr', []);
    }
}
