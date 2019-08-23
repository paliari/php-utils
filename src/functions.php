<?php
/**
 * Transforma uma string em array.
 *
 * @param string $str
 * @param string $delimiter
 *
 * @return array
 */
function w($str, $delimiter = ' ')
{
    return explode($delimiter, $str);
}

/**
 * @return int
 */
function microSeconds()
{
    return (int)(microtime(true) * 1000);
}
