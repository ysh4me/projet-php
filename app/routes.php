<?php

use App\Core\Router;
use App\Controllers\PhotoController;

$router = new Router();

$router->get('/', 'HomeController@index');

$router->get('/photo/upload', function() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Veuillez vous connecter.";
        header("Location: /login");
        exit;
    }

    $photoModel = new \App\Models\PhotoModel();
    $groups = $photoModel->getUserGroups($_SESSION['user_id']);

    require_once '../app/Views/upload_photo.php';
});

$router->post('/photo/upload', [PhotoController::class, 'uploadPhoto']);


return $router;
