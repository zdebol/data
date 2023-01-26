<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource;

use FSi\Component\DataSource\Exception\DataSourceExceptionInterface;
use FSi\Component\DataSource\Field\FieldInterface;
use FSi\Component\DataSource\Field\FieldViewInterface;

/**
 * DataSource abstracts fetching data from various sources. For more information
 * about usage please view README file.
 *
 * DataSource maintains communication with driver, manipulating fields (adding,
 * removing, etc.), calling DataSource extensions events, view creation and more.
 * It's first and main interface client will communicate with.
 *
 * @template T
 */
interface DataSourceInterface
{
    public const PARAMETER_FIELDS = 'fields';

    public function getName(): string;
    public function hasField(string $name): bool;
    /**
     * @param string $name
     * @param string $type
     * @param array<string,mixed> $options
     * @return DataSourceInterface<T>
     * @throws DataSourceExceptionInterface
     */
    public function addField(
        string $name,
        string $type,
        array $options = []
    ): DataSourceInterface;
    public function removeField(string $name): void;
    public function getField(string $name): FieldInterface;
    /**
     * @return array<FieldInterface>
     */
    public function getFields(): array;
    public function clearFields(): void;
    /**
     * @param int|null $max
     * @return DataSourceInterface<T>
     */
    public function setMaxResults(?int $max): DataSourceInterface;
    /**
     * @param int|null $first
     * @return DataSourceInterface<T>
     */
    public function setFirstResult(?int $first): DataSourceInterface;
    public function getMaxResults(): ?int;
    public function getFirstResult(): ?int;
    /**
     * Binds parameters to datasource. These could be any type of data that will be converted to array i.e by some
     * event subscriber
     *
     * @param mixed $parameters
     */
    public function bindParameters($parameters = []): void;
    /**
     * @return Result<T>
     */
    public function getResult(): Result;
    /**
     * @return DataSourceViewInterface<FieldViewInterface>
     */
    public function createView(): DataSourceViewInterface;
    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function getBoundParameters(): array;
}
