<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource\Driver;

use FSi\Component\DataSource\Exception\DataSourceException;
use InvalidArgumentException;

use function array_key_exists;
use function array_reduce;

/**
 * @template T
 * @template-implements DriverFactoryManagerInterface<T>
 */
final class DriverFactoryManager implements DriverFactoryManagerInterface
{
    /**
     * @var array<DriverFactoryInterface<T>>
     */
    private array $factories;

    /**
     * @param array<DriverFactoryInterface<T>> $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = array_reduce(
            $factories,
            static function (array $accumulator, DriverFactoryInterface $factory): array {
                $accumulator[$factory::getDriverType()] = $factory;
                return $accumulator;
            },
            []
        );
    }

    /**
     * @param string $driverType
     * @return DriverFactoryInterface<T>
     */
    public function getFactory(string $driverType): DriverFactoryInterface
    {
        if (false === array_key_exists($driverType, $this->factories)) {
            throw new DataSourceException("Driver \"{$driverType}\" does not exist.");
        }

        return $this->factories[$driverType];
    }
}
