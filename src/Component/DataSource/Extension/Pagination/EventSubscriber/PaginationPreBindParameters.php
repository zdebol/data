<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Extension\Pagination\EventSubscriber;

use FSi\Component\DataSource\Event\PreBindParameters;
use FSi\Component\DataSource\DataSourceEventSubscriberInterface;
use FSi\Component\DataSource\Extension\Pagination\PaginationExtension;

final class PaginationPreBindParameters implements DataSourceEventSubscriberInterface
{
    public static function getPriority(): int
    {
        return 0;
    }

    public function __invoke(PreBindParameters $event): void
    {
        $dataSource = $event->getDataSource();
        $parameters = $event->getParameters();

        $resultsPerPage = $parameters[$dataSource->getName()][PaginationExtension::PARAMETER_MAX_RESULTS]
            ?? $dataSource->getMaxResults();
        if (null !== $resultsPerPage) {
            $resultsPerPage = (int) $resultsPerPage;
        }

        $dataSource->setMaxResults($resultsPerPage);

        $page = (int) ($parameters[$dataSource->getName()][PaginationExtension::PARAMETER_PAGE] ?? 1);

        $dataSource->setFirstResult(($page - 1) * $dataSource->getMaxResults());
    }
}
