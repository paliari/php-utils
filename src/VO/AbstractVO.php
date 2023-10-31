<?php

namespace Paliari\Utils\VO;

use ArrayAccess, Countable, JsonSerializable;
use stdClass;

abstract class AbstractVO extends stdClass implements ArrayAccess, Countable, JsonSerializable
{
    public function __construct($attributes = [])
    {
        foreach ($attributes as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this as $k => $v) {
            if ($v instanceof AbstractVO) {
                $v = $v->toArray();
            }
            if (null !== $v) {
                $array[$k] = $v;
            }
        }

        return $array;
    }

    public function toJson(): bool|string
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            $offset = $this->count();
        }
        $this->set($offset, $value);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->$offset);
    }

    public function count(): int
    {
        return count($this->toArray());
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function get($name)
    {
        return $this->$name;
    }

    protected function set($key, $value)
    {
        $this->$key = $value;
    }
}
