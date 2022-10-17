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
abstract class AbstractModel implements HydratableInterface
{
    /**
     * @var array<string,mixed>
     */
    protected $attributes = [];

    public function __construct(array $data = [])
    {
        if ($data !== []) {
            $this->hydrate($data);
        }
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
        $this->attributes[$property] = $value;
        return $this;
    }

    public function getOrNull(string $property)
    {
        return $this->attributes[$property] ?? null;
    }

    public function getString(string $property): string
    {
        $value = $this->get($property);
        if (!is_string($value)) {
            throw $this->typeMismatchException($property, $value, 'string');
        }
        return $value;
    }

    public function getInt(string $property): int
    {
        $value = $this->get($property);
        if (!is_int($value)) {
            throw $this->typeMismatchException($property, $value, 'int');
        }
        return $value;
    }

    public function getFloat(string $property): float
    {
        $value = $this->get($property);
        if (!is_float($value)) {
            throw $this->typeMismatchException($property, $value, 'float');
        }
        return $value;
    }

    public function getBool(string $property): bool
    {
        $value = $this->get($property);
        if (!is_bool($value) && $value !== 0 && $value !== 1) {
            throw $this->typeMismatchException($property, $value, 'boolean');
        }

        return $value;
    }

    /**
     * @param string $property
     * @return array
     */
    public function getArray(string $property): array
    {
        $value = $this->get($property);
        if (!is_array($value)) {
            throw $this->typeMismatchException($property, $value, 'array');
        }
        return $value;
    }

    public function getInstanceOf(string $property, string $className): object
    {
        $value = $this->get($property);
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

    /**
     * @param array<string,mixed> $data
     */
    public function hydrate(array $data): void
    {
        $this->attributes = $data;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    protected function typeMismatchException(
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
}
