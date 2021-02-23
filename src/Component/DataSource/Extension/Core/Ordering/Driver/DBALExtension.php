<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Component\DataSource\Extension\Core\Ordering\Driver;

use FSi\Component\DataSource\Driver\Doctrine\DBAL\DBALDriver;
use FSi\Component\DataSource\Driver\Doctrine\DBAL\DBALFieldInterface;
use FSi\Component\DataSource\Event\DriverEvent;
use FSi\Component\DataSource\Field\FieldTypeInterface;
use InvalidArgumentException;

/**
 * Driver extension for ordering that loads fields extension.
 */
class DBALExtension extends DriverExtension
{
    public function getExtendedDriverTypes(): array
    {
        return [
            'doctrine-dbal'
        ];
    }

    public function preGetResult(DriverEvent\DriverEventArgs $event): void
    {
        $fields = $event->getFields();
        $sortedFields = $this->sortFields($fields);

        /** @var DBALDriver $driver */
        $driver = $event->getDriver();

        $qb = $driver->getQueryBuilder();
        foreach ($sortedFields as $fieldName => $direction) {
            $field = $fields[$fieldName];
            $qb->addOrderBy($this->getFieldName($field, $driver->getAlias()), $direction);
        }
    }

    /**
     * @param FieldTypeInterface&DBALFieldInterface $field
     * @param string $alias
     * @return string
     */
    private function getFieldName(FieldTypeInterface $field, string $alias): string
    {
        if (false === $field instanceof DBALFieldInterface) {
            throw new InvalidArgumentException("Field must be an instance of DoctrineField");
        }

        $name = true === $field->hasOption('field') ? $field->getOption('field') : $field->getName();

        if (true === $field->getOption('auto_alias') && false === strpos($name, ".")) {
            $name = "$alias.$name";
        }

        return $name;
    }
}
