<?php


namespace App\lib\Config;

use ArrayAccess;

/**
 * @property ConfigShard interface
 */
class ConfigShard extends \ArrayObject implements ArrayAccess
{
    private mixed $data;

    public function __construct($data)
    {
        if (is_array($data)) {
            parent::__construct($data);
        }
        $this->data = $data;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function __get(string $name)
    {
        $data = $this->data[$name] ?? null;
        if (is_array($data)) {
            return new self($data);
        }
        return $data;
    }

    public function offsetExists($offset): bool
    {
        return !!($this->data[$offset] ?? null);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data);
    }

    public function __toString()
    {
        return (string)$this->data;
    }

    public function toArray(): array
    {
        if (is_iterable($this->data)) {
            return (array)$this->data;
        }
        if (!is_null($this->data)) {
            return [$this->data];
        }
        return [];
    }
}