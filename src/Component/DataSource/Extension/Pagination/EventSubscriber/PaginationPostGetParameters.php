<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Extension\Pagination\EventSubscriber;

use FSi\Component\DataSource\Event\PostGetParameters;
use FSi\Component\DataSource\DataSourceEventSubscriberInterface;
use FSi\Component\DataSource\Extension\Pagination\PaginationExtension;

use function floor;

final class PaginationPostGetParameters implements DataSourceEventSubscriberInterface
{
    public static function getPriority(): int
    {
        return -1024;
    }

    public function __invoke(PostGetParameters $event): void
    {
        $dataSource = $event->getDataSource();
        $dataSourceName = $dataSource->getName();

        $parameters = $event->getParameters();
        $maxResults = $dataSource->getMaxResults();

        if (null !== $maxResults && 0 !== $maxResults) {
            $parameters[$dataSourceName][PaginationExtension::PARAMETER_MAX_RESULTS] = $maxResults;
            $current = $dataSource->getFirstResult();
            $page = (int) floor($current / $maxResults) + 1;
        } else {
            $page = 1;
        }

        unset($parameters[$dataSourceName][PaginationExtension::PARAMETER_PAGE]);
        if ($page > 1) {
            $parameters[$dataSourceName][PaginationExtension::PARAMETER_PAGE] = $page;
        }

        $event->setParameters($parameters);
    }
}
