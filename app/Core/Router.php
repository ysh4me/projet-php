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

    public function route(string $requestUri, string $requestMethod) {
        $uri = strtok($requestUri, '?');

        foreach ($this->routes[$requestMethod] as $route => $action) {
            $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); 

                if ($action instanceof \Closure) {
                    echo call_user_func_array($action, $matches);
                    return;
                }

                if (is_string($action)) {
                    [$controller, $method] = explode('@', $action);
                    $controller = "App\\Controllers\\$controller";

                    if (!class_exists($controller)) {
                        throw new \Exception("Controller introuvable : $controller");
                    }

                    if (!method_exists($controller, $method)) {
                        throw new \Exception("Méthode introuvable : $method dans le controller $controller");
                    }

                    $instance = new $controller();
                    echo call_user_func_array([$instance, $method], $matches);
                    return;
                }

                if (is_array($action) && count($action) === 2) {
                    [$controller, $method] = $action;

                    if (!class_exists($controller)) {
                        throw new \Exception("Classe du controller introuvable : $controller");
                    }

                    if (!method_exists($controller, $method)) {
                        throw new \Exception("Méthode introuvable : $method dans le controller $controller");
                    }

                    $instance = new $controller();
                    echo call_user_func_array([$instance, $method], $matches);
                    return;
                }
            }
        }

        http_response_code(404);
        echo '404 Not Found';
        exit;
    }

    public function getRoutes(): array {
        return $this->routes;
    }
}