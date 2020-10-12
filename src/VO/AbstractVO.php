<?php

namespace Paliari\Utils\VO;

use ArrayAccess, Countable, JsonSerializable;

abstract class AbstractVO implements ArrayAccess, Countable, JsonSerializable
{
    public function __construct($attributes = [])
    {
        foreach ($attributes as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function toArray()
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

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $offset = $this->count();
        }
        $this->set($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    public function count()
    {
        return count($this->toArray());
    }

    public function jsonSerialize()
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
