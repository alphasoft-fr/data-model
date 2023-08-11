<?php

namespace AlphaSoft\DataModel;

use AlphaSoft\DataModel\Hydrator\HydratableInterface;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use LogicException;
use function array_key_exists;
use function get_class;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;

/**
 * @package   Data Model
 * @author    F.Michel <fm.rejeb@alpha-soft.fr>
 * @license   https://opensource.org/licenses/MIT	MIT License
 * @link      https://www.alpha-soft.fr
 */
abstract class Model implements HydratableInterface
{
    /**
     * @var array<string,mixed>
     */
    protected $attributes;

    public function __construct(array $data = [])
    {
        $this->attributes = static::getDefaultAttributes();
        $this->hydrate($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function hydrate(array $data): void
    {
        foreach ($data as $property => $value) {
            $this->set($property, $value);
        }
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Convert attributes to a format suitable for database insertion/update.
     *
     * @return array<string,mixed>
     */
    public function toDb(): array
    {
        $dbData = [];

        foreach ($this->attributes as $property => $value) {
            $dbColumn = $this->mapPropertyToColumn($property);
            $dbData[$dbColumn] = $value;
        }

        return $dbData;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function get(string $property)
    {
        if (!array_key_exists($property, $this->attributes)) {
            throw new InvalidArgumentException(
                'No value for ' . $property . ' in ' . get_class($this)
            );
        }
        return $this->attributes[$property];
    }

    /**
     * @param string $property
     * @param $value mixed
     * @return mixed
     */
    public function set(string $property, $value): self
    {
        $property = $this->mapColumnToProperty($property);
        $this->attributes[$property] = $value;
        return $this;
    }

    public function getOrNull(string $property)
    {
        return $this->attributes[$property] ?? null;
    }
    public function getString(string $property, ?string $default = null): ?string
    {
        $value = $this->get($property);

        if ($value === null) {
            return $default;
        }

        if (!is_string($value)) {
            throw $this->typeMismatchException($property, $value, 'string');
        }

        return $value;
    }

    public function getInt(string $property, ?int $default = null): ?int
    {
        $value = $this->get($property);

        if ($value === null) {
            return $default;
        }

        if (!is_int($value)) {
            throw $this->typeMismatchException($property, $value, 'int');
        }

        return $value;
    }

    public function getFloat(string $property, ?float $default = null): ?float
    {
        $value = $this->get($property);

        if ($value === null) {
            return $default;
        }

        if (!is_float($value)) {
            throw $this->typeMismatchException($property, $value, 'float');
        }

        return $value;
    }

    public function getBool(string $property, ?bool $default = null): ?bool
    {
        $value = $this->get($property);

        if ($value === null) {
            return $default;
        }

        if (!is_bool($value) && $value !== 0 && $value !== 1) {
            throw $this->typeMismatchException($property, $value, 'boolean');
        }

        return $value;
    }

    public function getArray(string $property, array $default = []): ?array
    {
        $value = $this->get($property);

        if ($value === null) {
            return $default;
        }

        if (!is_array($value)) {
            throw $this->typeMismatchException($property, $value, 'array');
        }

        return $value;
    }

    public function getInstanceOf(string $property, string $className): ?object
    {
        $value = $this->get($property);

        if ($value === null) {
            return null;
        }

        if (!$value instanceof $className) {
            throw $this->typeMismatchException(
                $property,
                $value, "instance of {$className}"
            );
        }

        return $value;
    }

    public function getDateTime(
        string $property,
        string $format = 'Y-m-d H:i:s'
    ): ?DateTimeInterface
    {
        $value = $this->get($property);
        if ($value === null || $value instanceof DateTimeInterface) {
            return $value;
        }
        return DateTime::createFromFormat($format, $value);
    }

    private function typeMismatchException(
        string $property,
               $value,
        string $type
    ): LogicException
    {
        $given = gettype($value);
        if (is_object($value)) {
            $given = get_class($value);
        }
        return new LogicException(
            sprintf('%s must be %s, %s given', $property, $type, $given)
        );
    }

    /**
     * Maps an object property to its corresponding database column.
     *
     * @param string $property The object property to be mapped.
     * @return string The corresponding database column name.
     */
    protected function mapPropertyToColumn(string $property): string
    {
        $columnMapping = static::getDefaultColumnMapping();
        return $columnMapping[$property] ?? $property;
    }

    /**
     * Maps a database column to its corresponding object property.
     *
     * @param string $column The database column to be mapped.
     * @return string The corresponding object property name.
     */
    protected function mapColumnToProperty(string $column): string
    {
        $columnMapping = static::getDefaultColumnMapping();
        $reverseColumnMapping = array_flip($columnMapping);
        return $reverseColumnMapping[$column] ?? $column;
    }

    /**
     * Get the default attributes for the model.
     *
     * @return array<string,mixed> An associative array representing the default attributes.
     */
    abstract static protected function getDefaultAttributes(): array;

    /**
     * Get the default column mapping for the model.
     *
     * @return array<string,string> An associative array mapping object properties to database columns.
     */
    abstract static protected function getDefaultColumnMapping(): array;
}
