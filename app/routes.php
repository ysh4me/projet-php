<?php

use App\Core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');


return $router;
