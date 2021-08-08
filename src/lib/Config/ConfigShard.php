<?php


namespace App\lib\Config;

class ConfigShard implements \ArrayAccess
{
    private mixed $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function __get(string $name)
    {
        return new self($this->data[$name] ?? null);
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

    public function toArray()
    {
        if (is_iterable($this->data)) {
            return $this->data;
        }
        if (!is_null($this->data)) {
            return [$this->data];
        }
        return [];
    }
}