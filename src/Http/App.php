<?php

namespace Paliari\Utils\Http;

use Paliari\Utils\A,
    DomainException,
    Exception;

class App
{

    protected $request;
    protected $response;

    protected $routes = [];

    public function __construct()
    {
        if (!isset($GLOBALS['X-Time'])) {
            $GLOBALS['X-Time'] = microtime(true);
        }
        $this->response = new Response();
        $this->request  = new Request();
    }

    /**
     * @param string   $method HTTP method: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEADER
     * @param string   $route  eg: /users/{id}
     * @param callable $callable
     *
     * @return $this
     */
    public function map($method, $route, $callable)
    {
        $this->routes[strtoupper($method)][$this->routeToPattern($route)] = $callable;

        return $this;
    }

    /**
     * @param string   $route
     * @param callable $callable
     *
     * @return $this
     */
    public function get($route, $callable)
    {
        $this->map('GET', $route, $callable);

        return $this;
    }

    /**
     * @param string   $route
     * @param callable $callable
     *
     * @return $this
     */
    public function post($route, $callable)
    {
        $this->map('POST', $route, $callable);

        return $this;
    }

    /**
     * @param string   $route
     * @param callable $callable
     *
     * @return $this
     */
    public function put($route, $callable)
    {
        $this->map('PUT', $route, $callable);

        return $this;
    }

    /**
     * @param string   $route
     * @param callable $callable
     *
     * @return $this
     */
    public function patch($route, $callable)
    {
        $this->map('PATCH', $route, $callable);

        return $this;
    }

    /**
     * @param string   $route
     * @param callable $callable
     *
     * @return $this
     */
    public function delete($route, $callable)
    {
        $this->map('DELETE', $route, $callable);

        return $this;
    }

    protected function routeToPattern($route)
    {
        $replace = '([\w\-_]+)';
        preg_match_all('/{[\w]+}+/', $route, $a);
        foreach ($a[0] as $search) {
            $route = str_replace($search, $replace, $route);
        }
        $route = str_replace('/', '\/', $route);

        return '!^' . $route . '$!';
    }

    public function run()
    {
        try {
            $this->process();
        } catch (Exception $e) {
            if (200 == $this->response->code) {
                $this->response->code = 409;
            }
            $this->response->body = $this->toJson(['error' => $e->getMessage()]);
        }
        echo $this->response;
    }

    protected function process()
    {
        $patterns = A::get($this->routes, $this->request->getMethod(), []);
        foreach ($patterns as $pattern => $callable) {
            if (preg_match($pattern, $this->request->getPath(), $p)) {
                return $this->invoke($callable, array_slice($p, 1));
            }
        }
        $this->response->code = 404;
        throw new DomainException('Page not found!');
    }

    protected function invoke($callable, $params)
    {
        if (is_string($callable) && preg_match('/\w+\:\w+/', $callable)) {
            list($class, $method) = explode(':', $callable);
            $callable = [new $class(), $method];
        }
        $args = array_merge([$this->request, $this->response], $params);

        return call_user_func_array($callable, $args);
    }

    protected function toJson($content)
    {
        return json_encode($content);
    }

}
