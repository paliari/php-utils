<?php

namespace Paliari\Utils\Http;

use Paliari\Utils\A;

class Request
{

    protected $_payload;

    protected $_headers = [];

    public function getPath()
    {
        return explode('?', $this->getRequestUri())[0];
    }

    public function getRequestUri()
    {
        return A::get($_SERVER, 'REQUEST_URI', '/');
    }

    public function getMethod()
    {
        return A::get($_SERVER, 'REQUEST_METHOD', 'GET');
    }

    public function getBody($key = null)
    {
        if (null === $this->_payload) {
            $this->_payload = json_decode(file_get_contents('php://input'), true) ?: [];
        }
        if ($key) {
            return A::deepKey($this->_payload, $key);
        }

        return $this->_payload;
    }

    public function post($key = null)
    {
        if ($key) {
            return A::deepKey($_POST, $key);
        }

        return $_POST;
    }

    public function get($key = null)
    {
        if ($key) {
            return A::deepKey($_GET, $key);
        }

        return $_GET;
    }

    public function userAgent()
    {
        return A::get($_SERVER, 'HTTP_USER_AGENT', '');
    }

    public function ip()
    {
        return A::get($_SERVER, 'REMOTE_ADDR', '');
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getHeader($key)
    {
        return A::get($this->getHeaders(), $this->prepareHeaderKey($key));
    }

    protected function prepareHeaderKey($key)
    {
        return str_replace('-', '_', strtolower($key));
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        if (!$this->_headers) {
            foreach ($this->requestHeaders() as $k => $v) {
                $this->_headers[$this->prepareHeaderKey($k)] = $v;
            }
        }

        return $this->_headers;
    }

    protected function requestHeaders()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        if (!$headers) {
            foreach ($_SERVER as $k => $v) {
                if ('HTTP_' === substr($k, 0, 5)) {
                    $headers[substr($k, 5)] = $v;
                }
            }
        }

        return $headers;
    }

}
