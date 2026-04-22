<?php

namespace App;

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch($method, $path)
    {
        if (isset($this->routes[$method][$path])) {
            return $this->routes[$method][$path]();
        }

        // Check for parameterized routes
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $route);
            if (preg_match("^$pattern$", $path, $matches)) {
                array_shift($matches);
                return call_user_func_array($callback, $matches);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}

function view($name, $data = [])
{
    extract($data);
    $file = __DIR__ . '/../resources/views/' . str_replace('.', '/', $name) . '.blade.php';
    if (file_exists($file)) {
        include $file;
    } else {
        echo "View not found: $name";
    }
}

function redirect($path)
{
    header("Location: /coffee_haven$path");
    exit;
}

function route($name, $params = [])
{
    // Simple route name helper
    return "/coffee_haven$name";
}
