<?php
namespace Paliari\Utils;

class A
{

    /**
     * Merge arrays.
     *
     * @param array $a1
     * @param array $a2
     *
     * @return array
     */
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

    /**
     * Get deep key separate of '.'.
     * example: A::deepKey(['b' => ['b1' => 2]], 'b.b1')
     * return: 2
     *
     * @param array $content
     * @param       $key
     *
     * @return array|mixed|null
     */
    public static function deepKey(array $content, $key)
    {
        $keys = explode('.', $key);
        foreach ($keys as $k) {
            $content = isset($content[$k]) ? $content[$k] : null;
        }

        return $content;
    }

    /**
     * Convert array for simple array,
     * example: A::flatten(['a' => 1, 2, ['b' => 'b3', 'c' => ['c1', 'c2']]])
     * return: [1, 2, 'b3', 'c1', 'c2']
     *
     * @param array $array
     *
     * @return array
     */
    public static function flatten(array $array)
    {
        $flattened_array = [];
        array_walk_recursive($array, function ($a) use (&$flattened_array) {
            $flattened_array[] = $a;
        });

        return $flattened_array;
    }

}
