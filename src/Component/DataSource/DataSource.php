<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\DataSource;

use FSi\Component\DataSource\Driver\DriverInterface;
use FSi\Component\DataSource\Event as DataSourceEvent;
use FSi\Component\DataSource\Exception\DataSourceException;
use FSi\Component\DataSource\Field\Event as FieldEvent;
use FSi\Component\DataSource\Field\FieldInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

use function array_key_exists;
use function array_reduce;
use function count;
use function get_class;
use function gettype;
use function is_array;
use function preg_match;
use function sprintf;

/**
 * @template T
 * @template-implements DataSourceInterface<T>
 */
class DataSource implements DataSourceInterface
{
    /**
     * @var DriverInterface<T>
     */
    private DriverInterface $driver;
    private EventDispatcherInterface $eventDispatcher;
    private string $name;
    /**
     * @var array<FieldInterface>
     */
    private array $fields;
    /**
     * @var array<string, mixed>
     */
    private array $boundParameters;
    private ?int $maxResults;
    private ?int $firstResult;
    /**
     * Cache for methods that depends on fields data (cache is dropped whenever
     * any of fields is dirty, or fields have changed).
     *
     * @var array{
     *   parameters?: array<string, array<string, array<string, mixed>>>,
     *   result?: array{max_results: int|null, first_result: int|null, result: Result<T>}
     * }
     */
    private array $cache;
    /**
     * Flag set as true when fields or their data is modifying.
     *
     * @var bool
     */
    private bool $dirty;

    /**
     * @param DriverInterface<T> $driver
     */
    public function __construct(
        string $name,
        EventDispatcherInterface $eventDispatcher,
        DriverInterface $driver
    ) {
        if (1 !== preg_match('/^[\w]+$/', $name)) {
            throw new DataSourceException(
                "Name \"{$name}\" of data source may contain only word characters and digits."
            );
        }

        $this->name = $name;
        $this->eventDispatcher = $eventDispatcher;
        $this->driver = $driver;
        $this->boundParameters = [];
        $this->fields = [];
        $this->maxResults = null;
        $this->firstResult = null;
        $this->cache = [];
        $this->dirty = true;
    }

    public function hasField(string $name): bool
    {
        return array_key_exists($name, $this->fields);
    }

    public function addField(
        string $name,
        string $type,
        array $options = []
    ): DataSourceInterface {
        $field = $this->driver->getFieldType($type)->createField($this->name, $name, $options);

        $this->dirty = true;
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function removeField(string $name): void
    {
        if (false === array_key_exists($name, $this->fields)) {
            return;
        }

        unset($this->fields[$name]);
        $this->dirty = true;
    }

    public function getField(string $name): FieldInterface
    {
        if (false === $this->hasField($name)) {
            throw new DataSourceException(
                "There is no field with name \"{$name}\" in DataSource \"{$this->name}\""
            );
        }

        return $this->fields[$name];
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function clearFields(): void
    {
        $this->fields = [];
        $this->dirty = true;
    }

    public function bindParameters($boundParameters = []): void
    {
        $this->dirty = true;

        $preBindEvent = new DataSourceEvent\PreBindParameters($this, $boundParameters);
        $this->eventDispatcher->dispatch($preBindEvent);
        $parameters = $preBindEvent->getParameters();

        if (false === is_array($parameters)) {
            throw new DataSourceException(
                sprintf(
                    'Parameters after %s event must be an array but are %s.',
                    DataSourceEvent\PreBindParameters::class,
                    true === is_object($parameters) ? get_class($parameters) : gettype($parameters)
                )
            );
        }

        if (
            true === array_key_exists($this->name, $parameters)
            && true === is_array($parameters[$this->name])
        ) {
            $fieldsParameters = $parameters[$this->name][DataSourceInterface::PARAMETER_FIELDS] ?? [];
            foreach ($this->fields as $field) {
                $fieldName = $field->getName();
                $fieldRawValue = $fieldsParameters[$fieldName] ?? null;
                $this->boundParameters[$fieldName] = $fieldRawValue;

                $event = new FieldEvent\PreBindParameter($field, $fieldRawValue);
                $this->eventDispatcher->dispatch($event);

                $field->bindParameter($event->getParameter());
            }
        }
    }

    public function getResult(): Result
    {
        $this->checkFieldsClarity();

        if (
            true === array_key_exists('result', $this->cache)
            && $this->cache['result']['max_results'] === $this->getMaxResults()
            && $this->cache['result']['first_result'] === $this->getFirstResult()
        ) {
            return $this->cache['result']['result'];
        }

        $result = $this->driver->getResult(
            $this->fields,
            $this->getFirstResult(),
            $this->getMaxResults()
        );

        foreach ($this->getFields() as $field) {
            $field->setDirty(false);
        }

        // Creating cache.
        $this->cache['result'] = [
            'result' => $result,
            'first_result' => $this->getFirstResult(),
            'max_results' => $this->getMaxResults(),
        ];

        return $result;
    }

    public function setMaxResults(?int $max): DataSourceInterface
    {
        $this->dirty = true;
        $this->maxResults = $max;

        return $this;
    }

    public function setFirstResult(?int $first): DataSourceInterface
    {
        $this->dirty = true;
        $this->firstResult = $first;

        return $this;
    }

    public function getMaxResults(): ?int
    {
        return $this->maxResults;
    }

    public function getFirstResult(): ?int
    {
        return $this->firstResult;
    }

    public function createView(): DataSourceViewInterface
    {
        $event = new DataSourceEvent\PreBuildView($this->getFields());
        $this->eventDispatcher->dispatch($event);

        $view = new DataSourceView(
            $this->getName(),
            $event->getFields(),
            $this->getBoundParameters()
        );

        $this->eventDispatcher->dispatch(new DataSourceEvent\PostBuildView($this, $view));
        return $view;
    }

    public function getBoundParameters(): array
    {
        $this->checkFieldsClarity();
        if (true === array_key_exists('parameters', $this->cache)) {
            return $this->cache['parameters'];
        }

        $event = new DataSourceEvent\PostGetParameters($this, [
            $this->name => [
                DataSourceInterface::PARAMETER_FIELDS => $this->boundParameters
            ]
        ]);

        $this->eventDispatcher->dispatch($event);

        $parameters = self::cleanData($event->getParameters());
        $this->cache['parameters'] = $parameters;

        return $parameters;
    }

    /**
     * @param array<string,mixed> $array
     * @return array<string,mixed>
     */
    private static function cleanData(array $array): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            if (true === is_array($value)) {
                $newValue = self::cleanData($value);
                if (0 !== count($newValue)) {
                    $newArray[$key] = $newValue;
                }
            } elseif (is_scalar($value) && '' !== $value) {
                $newArray[$key] = $value;
            }
        }

        return $newArray;
    }

    /**
     * Checks if from last time some of data has changed, and if did, resets cache.
     */
    private function checkFieldsClarity(): void
    {
        // Initialize with dirty flag.
        $dirty = array_reduce($this->getFields(), static function (bool $dirty, FieldInterface $field): bool {
            return $dirty || $field->isDirty();
        }, $this->dirty);

        // If flag was set to dirty, or any of fields was dirty, reset cache.
        if (true === $dirty) {
            $this->cache = [];
            $this->dirty = false;
        }
    }
}
