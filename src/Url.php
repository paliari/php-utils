<?php
namespace Paliari\Utils;

class Url
{
    /**
     * http, https, file...
     *
     * @var string
     */
    public $scheme;

    /**
     * my_domain.com
     *
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $pass;

    /**
     * @var string
     */
    public $path;

    /**
     * After the question mark ?
     *
     * @var string
     */
    public $query;

    /**
     * After the hash mark #
     *
     * @var string
     */
    public $fragment;

    /**
     * @var string
     */
    public $url;

    /**
     * @param string $url
     *
     * @return static
     */
    public static function parse($url)
    {
        return new static($url);
    }

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        foreach (parse_url($url) as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Convert query string to array.
     *
     * @return array
     */
    public function queryToArray()
    {
        $array = [];
        parse_str($this->query, $array);

        return $array;
    }
}
