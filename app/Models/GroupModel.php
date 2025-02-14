<?php

namespace App\Models;

use PDO;

class GroupModel
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getUserGroups(string $userId): array
    {
        $query = "SELECT g.id, g.name, g.owner_id 
                  FROM group_members gm
                  JOIN `groups` g ON gm.group_id = g.id
                  WHERE gm.user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createGroup(string $groupName, string $ownerId): ?string
    {
        $query = "INSERT INTO `groups` (id, name, owner_id, created_at) 
                  VALUES (UUID(), :name, :owner_id, NOW())";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':name' => $groupName,
            ':owner_id' => $ownerId
        ]);

        return $this->pdo->lastInsertId();
    }

    public function deleteGroup(string $groupId, string $userId): bool
    {
        $query = "DELETE FROM `groups` WHERE id = :group_id AND owner_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':group_id' => $groupId,
            ':user_id' => $userId
        ]);
    }
}
