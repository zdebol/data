<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Component\DataSource\Fixtures;

use ArrayIterator;
use FSi\Component\DataSource\Result;
use Traversable;

final class UncountableTestResult implements Result
{
    /**
     * @return ArrayIterator<int,mixed>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator([]);
    }
}
