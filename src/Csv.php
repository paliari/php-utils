<?php

namespace Paliari\Utils;

use Exception;

/**
 * Class Csv
 * @package Paliari\Utils
 */
class Csv
{

    protected $delimiter = ",", $enclosure = '"', $escape_char = "\\";

    public function __construct($delimiter = ",", $enclosure = '"', $escape_char = "\\")
    {
        $this->delimiter   = $delimiter;
        $this->enclosure   = $enclosure;
        $this->escape_char = $escape_char;
    }

    /**
     * @param string $file_name
     * @param array  $rows
     * @param bool   $first_row_column_names
     *
     * @return int rows write
     * @throws Exception
     */
    public function create($file_name, $rows, $first_row_column_names = false)
    {
        $res   = $this->open($file_name, 'w');
        $count = 0;
        if ($first_row_column_names) {
            array_unshift($rows, array_keys($rows[0]));
        }
        foreach ($rows as $row) {
            if (false !== fputcsv($res, $row, $this->delimiter, $this->enclosure, $this->escape_char)) {
                $count++;
            } else {
                throw new Exception('Fail to write file!');
            }
        }
        fclose($res);

        return $count;
    }

    /**
     * @param string $file_name
     * @param bool   $first_row_column_names
     * @param null   $length
     *
     * @return array
     * @throws Exception
     */
    public function parse($file_name, $first_row_column_names = false, $length = null)
    {
        $rows = [];
        $res  = $this->open($file_name, 'r');
        $line = 1;
        if (false !== $res) {
            if ($first_row_column_names) {
                $names = fgetcsv($res, $length);
                $line++;
            }
            while ($row = fgetcsv($res, $length)) {
                $rows[] = $first_row_column_names ? $this->combine($names, $row, $line) : $row;
                $line++;
            }
        } else {
            throw new Exception('Fail to read file!');
        }
        fclose($res);

        return $rows;
    }

    protected function open($file, $mode)
    {
        return fopen($file, $mode);
    }

    protected function combine($keys, $values, $line)
    {
        if (count($keys) == count($values)) {
            return A::combine($keys, $values);
        } else {
            throw new Exception("Both parameters should have an equal number of elements in line $line!");
        }
    }

}
