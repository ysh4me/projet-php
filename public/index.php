<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = require '../app/routes.php';
$router->route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
