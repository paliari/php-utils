<?php

namespace Paliari\Utils;

abstract class AbstractSingleton
{

    protected static $_instances = [];

    /**
     * @return static
     */
    public static function i()
    {
        $className = get_called_class();
        if (!isset(static::$_instances[$className])) {
            static::$_instances[$className] = new static();
        }

        return static::$_instances[$className];
    }

}
