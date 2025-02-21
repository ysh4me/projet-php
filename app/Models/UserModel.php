<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserModel
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function createUser(string $firstname, string $lastname, string $username, string $email, string $password): bool
    {
        try {
            $query = "INSERT INTO users (id, first_name, last_name, username, email, password, is_verified, created_at) 
                      VALUES (UUID(), :firstname, :lastname, :username, :email, :password, FALSE, NOW())";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur SQL : " . $e->getMessage());
            return false;
        }
    }

    public function getUserByEmail(string $email): ?array
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function emailExists(string $email): bool
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function usernameExists(string $username): bool
    {
        $query = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function storeEmailVerificationToken(string $email, string $token): bool
    {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return false;
        }

        $query = "INSERT INTO email_verifications (id, user_id, token, expires_at) 
                  VALUES (UUID(), :user_id, :token, DATE_ADD(NOW(), INTERVAL 1 DAY))";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function verifyEmail(string $token): bool
    {
        $query = "SELECT user_id FROM email_verifications WHERE token = :token AND expires_at > NOW()";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $updateQuery = "UPDATE users SET is_verified = TRUE WHERE id = :user_id";
        $updateStmt = $this->pdo->prepare($updateQuery);
        $updateStmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_STR);
        
        return $updateStmt->execute();
    }

    public function storePasswordResetToken(string $email, string $token): bool
    {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return false;
        }

        $query = "INSERT INTO password_resets (id, user_id, token, expires_at) 
                VALUES (UUID(), :user_id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function isResetTokenValid(string $token): bool
    {
        $query = "SELECT user_id FROM password_resets WHERE token = :token AND expires_at > NOW()";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() !== false;
    }

    public function updatePassword(string $token, string $newPassword): bool
    {
        $query = "SELECT user_id FROM password_resets WHERE token = :token AND expires_at > NOW()";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $updateQuery = "UPDATE users SET password = :password WHERE id = :user_id";
        $updateStmt = $this->pdo->prepare($updateQuery);
        $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_STR);
        $updateStmt->execute();

        $deleteQuery = "DELETE FROM password_resets WHERE token = :token";
        $deleteStmt = $this->pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':token', $token, PDO::PARAM_STR);
        $deleteStmt->execute();

        return true;
    }

    public function generateJwtToken(string $userId): string
    {
        $secretKey = $_ENV['JWT_SECRET'];

        $payload = [
            'iss' => "http://localhost:8000",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24)
        ];

        return JWT::encode($payload, $secretKey, 'HS256');
    }

    public function verifyJwtToken(string $token): ?array
    {
        $secretKey = $_ENV['JWT_SECRET'];

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
