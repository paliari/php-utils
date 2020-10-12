<?php

namespace Paliari\Utils\VO;

class ParamsVO extends AbstractVO
{
    protected function set($key, $value)
    {
        parent::set($key, is_array($value) ? new static($value) : $value);
    }
}
