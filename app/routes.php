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
    $testUserId = "550e8400-e29b-41d4-a716-446655440000"; 
    // if (!isset($_SESSION['user_id'])) {
    //     $_SESSION['error'] = "Veuillez vous connecter.";
    //     header("Location: /login");
    //     exit;
    // }

    $photoModel = new \App\Models\PhotoModel();
    // $groups = $photoModel->getUserGroups($_SESSION['user_id']);
    $groups = $photoModel->getUserGroups($testUserId);

    require_once '../app/Views/upload_photo.php';
});

$router->post('/photo/upload', [PhotoController::class, 'uploadPhoto']);
$router->get('/groups', [GroupController::class, 'index']);
$router->post('/group/create', [GroupController::class, 'createGroup']);
$router->post('/group/delete', [GroupController::class, 'deleteGroup']);

$router->get('/photos', [PhotoController::class, 'showPhotos']);

$router->post('/photo/delete', [PhotoController::class, 'deletePhoto']);
$router->post('/photo/share', [PhotoController::class, 'generateShareLink']);
$router->get('/photo/view', [PhotoController::class, 'viewPhoto']);

return $router;
