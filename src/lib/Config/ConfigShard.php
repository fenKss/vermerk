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

    public function __get(string $name)
    {
        $data = $this->data[$name] ?? null;
        if (is_array($data)) {
            return new self($data);
        }
        return $data;
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