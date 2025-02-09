<?php

namespace App\Models;

use PDO;

class PhotoModel
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function savePhoto(string $userId, string $groupId, string $fileName, string $mimeType, int $size): bool
    {
        $query = "INSERT INTO photos (id, user_id, group_id, filename, mime_type, size, uploaded_at) 
                  VALUES (UUID(), :user_id, :group_id, :filename, :mime_type, :size, NOW())";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':user_id' => $userId,
            ':group_id' => $groupId,
            ':filename' => $fileName,
            ':mime_type' => $mimeType,
            ':size' => $size
        ]);
    }    

    public function getUserGroups(int $userId): array
    {
        $query = "SELECT g.id, g.name 
                  FROM group_members gm
                  JOIN `groups` g ON gm.group_id = g.id
                  WHERE gm.user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
