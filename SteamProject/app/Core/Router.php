<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function put(string $path, callable $handler): void
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete(string $path, callable $handler): void
    {
        $this->routes['DEL'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];

            call_user_func($handler);
            return;
        }

        $allowedMethods = [];

        foreach ($this->routes as $httpMethod => $routes) {
            if (isset($routes[$path])) {
                $allowedMethods[] = $httpMethod;
            }
        }

        if (!empty($allowedMethods)) {
            http_response_code(405);
            header('Allow: ' . implode(', ', $allowedMethods));
            echo "405 - Method Not Allowed";
            return;
        }

        http_response_code(404);
        echo "404 - Not Found";
    }
}

?>