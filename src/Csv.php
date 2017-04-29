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

    protected $resource;

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
     * @param string $mode
     *
     * @return int rows write
     * @throws Exception
     */
    public function create($file_name, $rows, $first_row_column_names = false, $mode = 'w')
    {
        $this->open($file_name, $mode);
        $count = 0;
        if ($first_row_column_names) {
            array_unshift($rows, array_keys($rows[0]));
        }
        foreach ($rows as $row) {
            $this->put($row);
        }

        return $count;
    }

    public function put($row)
    {
        $put = fputcsv($this->resource, $row, $this->delimiter, $this->enclosure, $this->escape_char);
        if (false === $put) {
            throw new Exception('Fail to write file!');
        }

        return $put;
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
        if ($first_row_column_names) {
            $names = fgetcsv($res, $length);
            $line++;
        }
        while ($row = fgetcsv($res, $length)) {
            $rows[] = $first_row_column_names ? $this->combine($names, $row, $line) : $row;
            $line++;
        }

        return $rows;
    }

    /**
     * @param string $file
     * @param string $mode
     *
     * @return resource
     * @throws Exception
     */
    public function open($file, $mode)
    {
        $this->resource = fopen($file, $mode);
        if (false === $this->resource) {
            throw new Exception('Fail to open file!');
        }

        return $this->resource;
    }

    public function close()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
            $this->resource = null;
        }

        return true;
    }

    protected function combine($keys, $values, $line)
    {
        if (count($keys) == count($values)) {
            return A::combine($keys, $values);
        } else {
            throw new Exception("Both parameters should have an equal number of elements in line $line!");
        }
    }

    public function __destruct()
    {
        $this->close();
    }

}
