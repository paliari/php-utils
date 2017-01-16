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
