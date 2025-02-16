<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use \Mailjet\Client;
use \Mailjet\Resources;
use App\Middleware\AuthMiddleware;

require_once __DIR__ . '/../../vendor/autoload.php';

class UserController extends Controller
{
    private UserModel $userModel;
    private Client $httpClient;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close(); 
        }
    
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    
        $this->userModel = new UserModel();
    }

    public function getProfile()
    {
        AuthMiddleware::authenticate(); 

        echo json_encode(["message" => "Accès autorisé", "user" => $_SESSION['user']]);
    }

    private function sendVerificationEmail(string $email, string $emailContent)
    {
        $apiKey = $_ENV['MJ_APIKEY_PUBLIC'] ?? null;
        $apiSecret = $_ENV['MJ_APIKEY_PRIVATE'] ?? null;
        $senderEmail = $_ENV['SENDER_EMAIL'] ?? null;

        if (!$apiKey || !$apiSecret || !$senderEmail) {
            die("Erreur : Clé API Mailjet ou email expéditeur introuvable !");
        }

        try {
            $mj = new Client($apiKey, $apiSecret, true, ['version' => 'v3.1']);

            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $senderEmail,
                            'Name' => "Buddy Shotz"
                        ],
                        'To' => [
                            [
                                'Email' => $email,
                                'Name' => "Utilisateur"
                            ]
                        ],
                        'Subject' => "Buddy Shotz - Validation de votre email",
                        'HTMLPart' => $emailContent
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);

            if (!$response->success()) {
                throw new \Exception("Échec de l'envoi de l'email : " . json_encode($response->getData(), JSON_PRETTY_PRINT));
            }

        } catch (\Exception $e) {
            echo json_encode(["error" => "Erreur lors de l'envoi de l'email : " . $e->getMessage()]);
            exit;
        }
    }

    private function sendEmail(string $email, string $subject, string $emailContent)
    {
        $apiKey = $_ENV['MJ_APIKEY_PUBLIC'] ?? null;
        $apiSecret = $_ENV['MJ_APIKEY_PRIVATE'] ?? null;
        $senderEmail = $_ENV['SENDER_EMAIL'] ?? null;

        if (!$apiKey || !$apiSecret || !$senderEmail) {
            die("Erreur : Clé API Mailjet ou email expéditeur introuvable !");
        }

        try {
            $mj = new Client($apiKey, $apiSecret, true, ['version' => 'v3.1']);

            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $senderEmail,
                            'Name' => "Buddy Shotz"
                        ],
                        'To' => [
                            [
                                'Email' => $email,
                                'Name' => "Utilisateur"
                            ]
                        ],
                        'Subject' => $subject,
                        'HTMLPart' => $emailContent
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);

            if (!$response->success()) {
                throw new \Exception("Échec de l'envoi de l'email : " . json_encode($response->getData(), JSON_PRETTY_PRINT));
            }

        } catch (\Exception $e) {
            echo json_encode(["error" => "Erreur lors de l'envoi de l'email : " . $e->getMessage()]);
            exit;
        }
    }

    public function register()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée."]);
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die(json_encode(["error" => "Échec de la validation CSRF."]));
        }    

        $inputData = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        $firstname = sanitizeInput($inputData['firstname'] ?? '');
        $lastname = sanitizeInput($inputData['lastname'] ?? '');
        $username = sanitizeInput($inputData['username'] ?? '');
        $email = filter_var($inputData['email'] ?? '', FILTER_VALIDATE_EMAIL) ? sanitizeInput($inputData['email']) : '';
        $password = $inputData['password'] ?? '';
        $passwordConfirm = $inputData['passwordConfirm'] ?? '';
        

        if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $_SESSION['error'] = ["Tous les champs sont obligatoires."];
            header("Location: /register");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = ["Adresse email invalide."];
            header("Location: /register");
            exit;
        }

        if ($this->userModel->emailExists($email)) {
            $_SESSION['error'] = ["Cet email est déjà utilisé."];
            header("Location: /register");
            exit;
        }

        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = ["Ce nom d'utilisateur est déjà utilisé."];
            header("Location: /register");
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = ["Les mots de passe ne correspondent pas."];
            header("Location: /register");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $userCreated = $this->userModel->createUser($firstname, $lastname, $username, $email, $hashedPassword);

        if ($userCreated) {
            $verificationToken = bin2hex(random_bytes(32));
            $this->userModel->storeEmailVerificationToken($email, $verificationToken);
        
            $verificationLink = $_ENV['APP_URL'] . "/verify-email?token=" . urlencode($verificationToken);
        
            ob_start();
            require __DIR__ . "/../Views/emails/verification_email.php";
            $emailContent = ob_get_clean();
        
            $this->sendVerificationEmail($email, $emailContent);
        
            $_SESSION['success'] = "Inscription réussie ! Un email de validation a été envoyé.";
            header("Location: /login"); 
            exit;
        }    
    }

    public function verifyEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $_SESSION['error'] = ["Méthode non autorisée."];
            header("Location: /login");
            exit;
        }
    
        $token = $_GET['token'] ?? '';
    
        if (empty($token)) {
            $_SESSION['error'] = ["Token invalide."];
            header("Location: /login");
            exit;
        }
    
        if ($this->userModel->verifyEmail($token)) {
            $_SESSION['success'] = "Votre email a été validé. Vous pouvez maintenant vous connecter.";
            header("Location: /login");
        } else {
            $_SESSION['error'] = ["Token invalide ou expiré."];
            header("Location: /login");
        }
        exit;
    }

    public function login()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée."]);
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die(json_encode(["error" => "Échec de la validation CSRF."]));
        }
    

        $inputData = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        $email = filter_var($inputData['email'] ?? '', FILTER_VALIDATE_EMAIL) ? sanitizeInput($inputData['email']) : '';
        $password = sanitizeInput($inputData['password'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = ["Tous les champs sont obligatoires."];
            header("Location: /login");
            exit;
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = ["Identifiants incorrects."];
            header("Location: /login");
            exit;
        }

        if (!$user['is_verified']) {
            $_SESSION['error'] = ["Veuillez valider votre email avant de vous connecter."];
            header("Location: /login");
            exit;
        }

        $token = $this->userModel->generateJwtToken($user['id']);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'firstname' => $user['first_name'], 
            'lastname' => $user['last_name'],
            'username' => $user['username'],
            'email' => $user['email']
        ];
                $_SESSION['token'] = $token;
    
        header("Location: /");
        exit;    
    }

    public function logout()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION = []; 
            session_destroy(); 
    
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 42000, '/');
            }
        }
    
        header("Location: /login");
        exit;
    }

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token) || !$this->userModel->isResetTokenValid($token)) {
            $_SESSION['error'] = "Lien invalide ou expiré.";
            header("Location: /forgot-password");
            exit;
        }

        require_once __DIR__ . '/../Views/reset_password.php';
    }


    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée."]);
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die(json_encode(["error" => "Échec de la validation CSRF."]));
        }
    

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? sanitizeInput($_POST['email']) : '';

        if (empty($email)) {
            $_SESSION['error'] = "Veuillez entrer un email.";
            header("Location: /forgot-password");
            exit;
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            $_SESSION['error'] = "Aucun compte trouvé avec cet email.";
            header("Location: /forgot-password");
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $this->userModel->storePasswordResetToken($email, $token);

        $resetLink = $_ENV['APP_URL'] . "/reset-password?token=" . urlencode($token);

        ob_start();
        require __DIR__ . "/../Views/emails/reset_password_email.php";
        $emailContent = ob_get_clean();

        $this->sendEmail($email, "Réinitialisation du mot de passe", $emailContent);

        $_SESSION['success'] = "Un email de réinitialisation a été envoyé.";
        header("Location: /forgot-password");
        exit;
    }

    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée."]);
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die(json_encode(["error" => "Échec de la validation CSRF."]));
        }
    

        $token = sanitizeInput($_POST['token'] ?? '');
        $password = sanitizeInput($_POST['password'] ?? '');
        $passwordConfirm = sanitizeInput($_POST['passwordConfirm'] ?? '');
        

        if (empty($token) || empty($password) || empty($passwordConfirm)) {
            $_SESSION['error'] = "Tous les champs sont obligatoires.";
            header("Location: /reset-password?token=" . urlencode($token));
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
            header("Location: /reset-password?token=" . urlencode($token));
            exit;
        }

        if ($this->userModel->updatePassword($token, $password)) {
            $_SESSION['success'] = "Mot de passe mis à jour. Vous pouvez vous connecter.";
            header("Location: /login");
            exit;
        } else {
            $_SESSION['error'] = "Le lien est invalide ou expiré.";
            header("Location: /forgot-password");
            exit;
        }
    }


    
    
    
}
