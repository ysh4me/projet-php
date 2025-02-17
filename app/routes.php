<?php

use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\PhotoController;
use App\Controllers\GroupController;

$router = new Router();

$router->get('/', 'HomeController@index');

// Authentification
$router->get('/register', function() {
    require_once '../app/Views/authentification/register.php';
});

$router->post('/register', [UserController::class, 'register']);

$router->get('/login', function() {
    require_once '../app/Views/authentification/login.php';
});

$router->post('/login', [UserController::class, 'login']);
$router->get('/logout', [UserController::class, 'logout']);
$router->get('/verify-email', 'UserController@verifyEmail');

$router->get('/forgot-password', function() {
    require_once '../app/Views/forgot_password.php';
});

$router->post('/forgot-password', [UserController::class, 'forgotPassword']);
$router->get('/reset-password', [UserController::class, 'resetPassword']);
$router->post('/update-password', [UserController::class, 'updatePassword']);

// Photos
$router->get('/photos', [PhotoController::class, 'showPhotos']);
$router->get('/upload-photo', function() {
    require_once '../app/Views/upload_photo.php';
});
$router->post('/photo/upload', [PhotoController::class, 'uploadPhoto']);
$router->post('/photo/delete', [PhotoController::class, 'deletePhoto']);
$router->post('/photo/share', [PhotoController::class, 'generateShareLink']);
$router->get('/photo/view', [PhotoController::class, 'viewPhoto']);

// Groupes
$router->get('/groups', [GroupController::class, 'index']);
$router->post('/group/create', [GroupController::class, 'createGroup']);
$router->post('/group/delete', [GroupController::class, 'deleteGroup']);

return $router;
