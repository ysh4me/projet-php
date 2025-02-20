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

// upload des photos
$router->get('/photo/upload', function() {
    session_start();

    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        $_SESSION['error'] = "Veuillez vous connecter.";
        header("Location: /login");
        exit;
    }
    

    $photoModel = new \App\Models\PhotoModel();
    $groups = $photoModel->getUserGroups($_SESSION['user_id']);

    require_once '../app/Views/upload_photo.php';
});

$router->post('/photo/upload', [PhotoController::class, 'uploadPhoto']);

$router->get('/albums', [GroupController::class, 'index']);
$router->post('/album/create', [GroupController::class, 'createGroup']);
$router->post('/album/delete', [GroupController::class, 'deleteAlbum']);
$router->post('/album/update', [GroupController::class, 'updateAlbum']);
$router->post('/album/update-permission', [GroupController::class, 'updateSharePermission']);
$router->get('/album/get-permission', [GroupController::class, 'getSharePermission']);
$router->post('/album/share', [GroupController::class, 'generateShareLink']);
$router->get('/album/view', function() {
    $controller = new GroupController();
    return $controller->viewSharedAlbum();
});


$router->get('/photos', [PhotoController::class, 'showPhotos']);

$router->post('/photo/delete', [PhotoController::class, 'deletePhoto']);
$router->post('/photo/share', [PhotoController::class, 'generateShareLink']);
$router->get('/photo/view', [PhotoController::class, 'viewPhoto']);

return $router;
