<?php

namespace App\Core;

class Request {
    public function isMethod(string $method): bool {
        return $_SERVER['REQUEST_METHOD'] === $method;
    }

    public function uri(): string {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        return rtrim($uri, '/');
    }

    public function input(string $name, string $fallback): string {
        if (isset ($_REQUEST[$name])) {
            return $fallback;
        }

        return $_REQUEST[$name];
    }

    public function json(): array {
        if ($this->isMethod('GET')) {
            return [];
        }

        return json_decode(file_get_contents('php://input'), true);
    }

    public function file(string $key): array {
        if (!isset($_FILES[$key])) {
            return [];
        }

        return $_FILES[$key];
    }

}