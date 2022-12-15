<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Field;

use FSi\Component\DataSource\DataSourceInterface;
use FSi\Component\DataSource\Field\Type\FieldTypeInterface;

interface FieldInterface
{
    public function getType(): FieldTypeInterface;
    public function getName(): string;
    public function getDataSourceName(): string;
    /**
     * @param string $name
     * @return mixed
     */
    public function getOption(string $name);
    public function hasOption(string $name): bool;
    /**
     * @param array<string, array<string, array<string, mixed>>> $parameters
     * @return void
     */
    public function bindParameters(array $parameters): void;
    /**
     * @return mixed
     */
    public function getParameter();
    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function getParameters(): array;
    public function isDirty(): bool;
    public function setDirty(bool $dirty = true): void;
}
