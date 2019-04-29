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
     * Add middleware
     *
     * This method prepends new middleware to the application middleware stack.
     *
     * @param callable $callable Any callable that accepts three arguments:
     *                           1. A Request object
     *                           2. A Response object
     *                           3. A "next" middleware callable
     *
     * @return $this
     */
    public function add($callable, $route = '/')
    {
        return $this->addMiddleware($callable, $this->routeToPattern($route, ''));
    }

    /**
     * @param string   $method HTTP method: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEADER
     * @param string   $route eg: /users/{id}
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

    protected function routeToPattern($route, $end = '$')
    {
        $replace = '([\w\-_]+)';
        preg_match_all('/{[\w]+}+/', $route, $a);
        foreach ($a[0] as $search) {
            $route = str_replace($search, $replace, $route);
        }
        $route = str_replace('/', '\/', $route);

        return '!^' . $route . $end . '!';
    }

    public function run()
    {
        try {
            $this->callMiddleware($this->request, $this->response);
        } catch (Exception $e) {
            if (200 == $this->response->code) {
                $this->response->code = 409;
            }
            $this->response->body = $this->toJson(['error' => $e->getMessage()]);
        }
        echo $this->response;
    }

    protected function runRoute(Request $request, Response $response)
    {
        $patterns = A::get($this->routes, $request->getMethod(), []);
        foreach ($patterns as $pattern => $callable) {
            if (preg_match($pattern, $request->getPath(), $p)) {
                return $this->invoke($callable, array_slice($p, 1));
            }
        }
        $response->code = 404;
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

    protected $_middleware = [];

    protected function middleware($pattern)
    {
        return A::get($this->_middleware, $pattern, $this);
    }

    protected function addMiddleware(callable $callable, $pattern)
    {
        $next                        = $this->middleware($pattern);
        $this->_middleware[$pattern] = function (Request $request, Response $response) use ($callable, $next) {
            $result = call_user_func($callable, $request, $response, $next);
            if (!$result instanceof Response) {
                throw new DomainException('Middleware must return instance of Response!');
            }

            return $result;
        };

        return $this;
    }

    /**
     * @param  Request  $request A request object
     * @param  Response $response A response object
     *
     * @return Response
     */
    protected function callMiddleware(Request $request, Response $response)
    {
        foreach ($this->_middleware as $pattern => $callable) {
            if (preg_match($pattern, $request->getPath())) {
                $start = $this->middleware($pattern);

                return $start($request, $response);
            }
        }

        return $this->runRoute($request, $response);
    }

    public function __invoke(Request $request, Response $response)
    {
        return $this->runRoute($request, $response);
    }

}
