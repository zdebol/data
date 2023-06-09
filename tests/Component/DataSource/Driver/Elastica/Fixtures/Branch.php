<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\FSi\Component\DataSource\Driver\Elastica\Fixtures;

class Branch
{
    private ?int $id;
    private ?int $idx;

    public function __construct(?int $id, ?int $idx = null)
    {
        $this->id = $id;
        $this->idx = $idx;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdx(): ?int
    {
        return $this->idx;
    }
}
