<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$helpersPath = __DIR__ . '/../app/Core/helpers.php';
if (file_exists($helpersPath)) {
    require_once $helpersPath;
} else {
    die("Erreur : Le fichier helpers.php est introuvable.");
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad(); 

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($exception) {
    http_response_code(500);
    echo json_encode([
        "error" => "Une erreur interne s'est produite.",
        "details" => escape($exception->getMessage()) 
    ]);
    exit;
});

$requestUri = strtok($_SERVER['REQUEST_URI'], '?'); 

$router = require '../app/routes.php';
$router->route($requestUri, $_SERVER['REQUEST_METHOD']);
