<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;

class AuthMiddleware
{
    public static function authenticate()
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            http_response_code(401);
            echo json_encode(["error" => "Token manquant"]);
            exit;
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $jwt = explode(" ", $authHeader)[1] ?? null;

        if (!$jwt) {
            http_response_code(401);
            echo json_encode(["error" => "Format du token invalide"]);
            exit;
        }

        $userModel = new UserModel();
        $userData = $userModel->verifyJwtToken($jwt);

        if (!$userData) {
            http_response_code(401);
            echo json_encode(["error" => "Token invalide ou expiré"]);
            exit;
        }

        $_SESSION['user'] = $userData;
    }
}
