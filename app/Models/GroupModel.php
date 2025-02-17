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
        try {
            $this->pdo->beginTransaction();

            // Générer un UUID pour le groupe
            $groupId = bin2hex(random_bytes(16));
            
            // Créer le groupe
            $query = "INSERT INTO `groups` (id, name, owner_id, created_at) 
                      VALUES (:id, :name, :owner_id, NOW())";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':id' => $groupId,
                ':name' => $groupName,
                ':owner_id' => $ownerId
            ]);

            // Ajouter le propriétaire comme membre du groupe
            $query = "INSERT INTO group_members (group_id, user_id, joined_at) 
                      VALUES (:group_id, :user_id, NOW())";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':group_id' => $groupId,
                ':user_id' => $ownerId
            ]);

            $this->pdo->commit();
            return $groupId;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de la création du groupe : " . $e->getMessage());
            return null;
        }
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
