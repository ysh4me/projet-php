<?php

namespace App\Core;

class Router {
    private $routes = [];

    public function get($uri, $controller) {
        $this->routes['GET'][$uri] = $controller;
    }

    public function post($uri, $controller): void {
        $this->routes['POST'][$uri] = $controller;
    }

    public function put($uri, $controller): void {
        $this->routes['PUT'][$uri] = $controller;
    }

    public function patch($uri, $controller): void {
        $this->routes['PATCH'][$uri] = $controller;
    }

    public function delete($uri, $controller): void {
        $this->routes['DELETE'][$uri] = $controller;
    }

    public function route($requestUri, $requestMethod) {
        $uri = strtok($requestUri, '?');
        $action = $this->routes[$requestMethod][$uri] ?? null;

        if(!$action) {
            http_response_code(404);
            echo '404 Not Found';
            exit;
        }

        [$controller, $method] = explode('@', $action);
        $controller = "App\\Controllers\\$controller";

        if (!class_exists($controller)) {
            throw new \Exception("Controller not found: $controller");
        }

        if (!method_exists($controller, $method)) {
            throw new \Exception("Method not found: $method in controller $controller");
        }

        $instance = new $controller();
        echo $instance->$method();
    }

    public function getRoutes(): array {
        return $this->routes;
    }
}