<?php

namespace AlphaSoft\DataModel;

use ArrayObject as PHPArrayObject;

class ArrayObject extends PHPArrayObject
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function hydrate(array $data): void
    {
        $this->exchangeArray($data);
    }

    /**
     * @param string $property
     * @param $value mixed
     * @return mixed
     */
    public function set(string $property, $value): self
    {
        $this->offsetSet($property,$value);
        return $this;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function get(string $property)
    {
        if (!$this->offsetExists($property)) {
            throw new \InvalidArgumentException(
                'No value for ' . $property . ' in ' . get_class($this)
            );
        }
        return $this->offsetGet($property);
    }

    public function getOrNull(string $property)
    {
        return $this->offsetGet($property) ?? null;
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
    ): ?\DateTimeInterface
    {
        $value = $this->get($property);
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }
        return \DateTime::createFromFormat($format, $value);
    }

    private function typeMismatchException(
        string $property,
               $value,
        string $type
    ): \LogicException
    {
        $given = gettype($value);
        if (is_object($value)) {
            $given = get_class($value);
        }
        return new \LogicException(
            sprintf('%s must be %s, %s given', $property, $type, $given)
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return iterator_to_array($this->getIterator());
    }
}
