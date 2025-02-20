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
            error_log("Mailjet API non configurée correctement.");
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
    
            error_log("Tentative d'envoi d'email à : $email");
    
            $response = $mj->post(Resources::$Email, ['body' => $body]);
    
            if (!$response->success()) {
                error_log("Échec de l'envoi de l'email : " . json_encode($response->getData(), JSON_PRETTY_PRINT));
                throw new \Exception("Échec de l'envoi de l'email.");
            } else {
                error_log("Email envoyé avec succès !");
            }
    
        } catch (\Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
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
            $_SESSION['error']['global'] = "Méthode non autorisée.";
            header("Location: /register");
            exit;
        }
    
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error']['global'] = "Échec de la validation CSRF.";
            header("Location: /register");
            exit;
        }
    
        $firstname = sanitizeInput($_POST['firstname'] ?? '');
        $lastname = sanitizeInput($_POST['lastname'] ?? '');
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? sanitizeInput($_POST['email']) : '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['passwordConfirm'] ?? '';
    
        if (empty($firstname)) {
            $_SESSION['error']['firstname'] = "Le prénom est requis.";
        }
    
        if (empty($lastname)) {
            $_SESSION['error']['lastname'] = "Le nom est requis.";
        }
    
        if (empty($username)) {
            $_SESSION['error']['username'] = "Le nom d'utilisateur est requis.";
        }
    
        if (empty($email)) {
            $_SESSION['error']['email'] = "L'email est requis.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error']['email'] = "Adresse email invalide.";
        } elseif ($this->userModel->emailExists($email)) {
            $_SESSION['error']['email'] = "Cet email est déjà utilisé.";
        }
    
        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error']['username'] = "Ce nom d'utilisateur est déjà utilisé.";
        }
    
        if (empty($password)) {
            $_SESSION['error']['password'] = "Le mot de passe est requis.";
        }
    
        if (empty($passwordConfirm)) {
            $_SESSION['error']['passwordConfirm'] = "La confirmation du mot de passe est requise.";
        } elseif ($password !== $passwordConfirm) {
            $_SESSION['error']['passwordConfirm'] = "Les mots de passe ne correspondent pas.";
        }
    
        if (!empty($_SESSION['error'])) {
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
            include __DIR__ . "/../Views/emails/verification_email.php";
            $emailContent = ob_get_clean();
    
            $this->sendVerificationEmail($email, $emailContent);
    
            require_once __DIR__ . '/../Views/email_confirmation.php';
            exit;
        } else {
            $_SESSION['error']['global'] = "Erreur lors de l'inscription. Veuillez réessayer.";
            header("Location: /register");
            exit;
        }
    }

    public function verifyEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $_SESSION['error']['global'] = "Méthode non autorisée.";
            header("Location: /");
            exit;
        }
    
        $token = $_GET['token'] ?? '';
    
        if (empty($token)) {
            $_SESSION['error']['global'] = "Token invalide.";
            header("Location: /");
            exit;
        }
    
        if ($this->userModel->verifyEmail($token)) {
            require_once __DIR__ . '/../Views/email_verified.php';
            exit;
        } else {
            $_SESSION['error']['global'] = "Token invalide ou expiré.";
            header("Location: /");
            exit;
        }
    }
    

    public function login()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $_SESSION['error']['global'] = "Méthode non autorisée.";
            session_write_close(); 
            header("Location: /login");
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error']['global'] = "Échec de la validation CSRF.";
            session_write_close();
            header("Location: /login");
            exit;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? sanitizeInput($_POST['email']) : '';
        $password = sanitizeInput($_POST['password'] ?? '');

        if (empty($email)) {
            $_SESSION['error']['email'] = "L'email est requis.";
        } else {
            $_SESSION['old_input']['email'] = $_POST['email'];
        }
        

        if (empty($password)) {
            $_SESSION['error']['password'] = "Le mot de passe est requis.";
        }

        if (!empty($_SESSION['error'])) {
            session_write_close();
            header("Location: /login");
            exit;
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error']['global'] = "Identifiants incorrects.";
            session_write_close();
            header("Location: /login");
            exit;
        }

        if (!$user['is_verified']) {
            $_SESSION['error']['global'] = "Veuillez valider votre email avant de vous connecter.";
            session_write_close();
            header("Location: /login");
            exit;
        }

        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['user'] = [
            'id' => $user['id'],
            'firstname' => $user['first_name'], 
            'lastname' => $user['last_name'],
            'username' => $user['username'],
            'email' => $user['email']
        ];
        $_SESSION['token'] = $this->userModel->generateJwtToken($user['id']);

        session_write_close();
        header("Location: /albums");
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

        require_once __DIR__ . '/../Views/authentification/reset_password.php';
    }


    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $_SESSION['error']['global'] = "Méthode non autorisée.";
            header("Location: /forgot-password");
            exit;
        }
    
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error']['global'] = "Échec de la validation CSRF.";
            header("Location: /forgot-password");
            exit;
        }
    
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? sanitizeInput($_POST['email']) : '';
    
        if (empty($email)) {
            $_SESSION['error']['email'] = "Veuillez entrer un email.";
            header("Location: /forgot-password");
            exit;
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error']['email'] = "Adresse email invalide.";
            header("Location: /forgot-password");
            exit;
        }
    
        $user = $this->userModel->getUserByEmail($email);
    
        if (!$user) {
            $_SESSION['error']['email'] = "Aucun compte trouvé avec cet email.";
            header("Location: /forgot-password");
            exit;
        }
    
        $resetToken = bin2hex(random_bytes(32));
        $this->userModel->storePasswordResetToken($email, $resetToken);
    
        $resetLink = $_ENV['APP_URL'] . "/reset-password?token=" . urlencode($resetToken);
    
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
            $_SESSION['error']['global'] = "Échec de la validation CSRF.";
            header("Location: /reset-password?token=" . urlencode($_POST['token']));
            exit;
        }
    
        $token = sanitizeInput($_POST['token'] ?? '');
        $password = sanitizeInput($_POST['password'] ?? '');
        $passwordConfirm = sanitizeInput($_POST['passwordConfirm'] ?? '');
    
        if (empty($token)) {
            $_SESSION['error']['global'] = "Le lien de réinitialisation est invalide.";
            header("Location: /forgot-password");
            exit;
        }
    
        if (empty($password)) {
            $_SESSION['error']['password'] = "Le mot de passe est requis.";
        }
    
        if (empty($passwordConfirm)) {
            $_SESSION['error']['passwordConfirm'] = "Veuillez confirmer votre mot de passe.";
        }
    
        if (!empty($_SESSION['error'])) {
            header("Location: /reset-password?token=" . urlencode($token));
            exit;
        }
    
        if ($password !== $passwordConfirm) {
            $_SESSION['error']['passwordConfirm'] = "Les mots de passe ne correspondent pas.";
            header("Location: /reset-password?token=" . urlencode($token));
            exit;
        }
    
        if (!$this->userModel->isResetTokenValid($token)) {
            $_SESSION['error']['global'] = "Le lien est invalide ou expiré.";
            header("Location: /forgot-password");
            exit;
        }
    
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        if ($this->userModel->updatePassword($token, $hashedPassword)) {
            $_SESSION['success'] = "Mot de passe mis à jour avec succès ! Vous pouvez maintenant vous connecter.";
            header("Location: /login");
            exit;
        } else {
            $_SESSION['error']['global'] = "Une erreur s'est produite lors de la mise à jour.";
            header("Location: /reset-password?token=" . urlencode($token));
            exit;
        }
    }

}
