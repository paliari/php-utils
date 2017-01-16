<?php
namespace Paliari\Utils;

class A
{

    public static function merge($a1, $a2)
    {
        $a1 = (array)$a1;
        $a2 = (array)$a2;
        foreach ($a1 as $k => $v) {
            if (is_array($v) && isset($a2[$k])) {
                $a1[$k] = $a2[$k] = static::merge($v, $a2[$k]);
            }
        }

        return array_merge($a1, $a2);
    }

    public static function deepKey(array $content, $key)
    {
        $keys = explode('.', $key);
        foreach ($keys as $k) {
            $content = isset($content[$k]) ? $content[$k] : null;
        }

        return $content;
    }

    public static function flatten(array $array)
    {
        $flattened_array = [];
        array_walk_recursive($array, function ($a) use (&$flattened_array) {
            $flattened_array[] = $a;
        });

        return $flattened_array;
    }

}
