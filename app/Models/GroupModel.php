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
              FROM `groups` g
              LEFT JOIN group_members gm ON g.id = gm.group_id 
              WHERE g.owner_id = :user_id OR gm.user_id = :user_id
              GROUP BY g.id"; 
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':user_id' => $userId]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function createGroup(string $groupName, string $ownerId): ?string
    {
        $this->pdo->beginTransaction();
    
        try {
            $checkUserQuery = "SELECT id FROM users WHERE id = :owner_id";
            $checkStmt = $this->pdo->prepare($checkUserQuery);
            $checkStmt->execute([':owner_id' => $ownerId]);
    
            if ($checkStmt->rowCount() === 0) {
                throw new \Exception("L'utilisateur propriétaire du groupe n'existe pas.");
            }
    
            $groupId = bin2hex(random_bytes(16));
    
            $query = "INSERT INTO `groups` (id, name, owner_id, created_at) 
                      VALUES (:id, :name, :owner_id, NOW())";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':id' => $groupId,
                ':name' => $groupName,
                ':owner_id' => $ownerId
            ]);
    
            $insertMemberQuery = "INSERT INTO group_members (id, group_id, user_id, role, can_upload, joined_at) 
                                  VALUES (UUID(), :group_id, :user_id, 'owner', 1, NOW())";
            $stmt = $this->pdo->prepare($insertMemberQuery);
            $stmt->execute([
                ':group_id' => $groupId,
                ':user_id' => $ownerId
            ]);
    
            $this->pdo->commit();
    
            return $groupId;
        } catch (\Exception $e) {
            $this->pdo->rollBack(); 
            throw new \Exception("Erreur lors de la création du groupe : " . $e->getMessage());
        }
    }    

    public function updateGroupName(string $groupId, string $newName): bool
    {
        $query = "UPDATE `groups` SET name = :new_name WHERE id = :group_id";
        $stmt = $this->pdo->prepare($query);
    
        return $stmt->execute([
            ':new_name' => $newName,
            ':group_id' => $groupId
        ]);
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

    public function getExistingShareLink(string $albumId): ?string
    {
        $query = "SELECT share_token FROM album_shares WHERE album_id = :album_id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':album_id' => $albumId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['share_token'] : null;
    }


    public function saveShareLink(string $albumId, string $shareToken): bool
    {
        $query = "INSERT INTO album_shares (id, album_id, share_token, created_at) 
                  VALUES (UUID(), :album_id, :share_token, NOW())";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':album_id' => $albumId,
            ':share_token' => $shareToken
        ]);
    }
    
    public function getAlbumByToken(string $shareToken): ?array
    {
        $query = "SELECT g.id, g.name, g.owner_id 
                  FROM album_shares s 
                  JOIN `groups` g ON s.album_id = g.id 
                  WHERE s.share_token = :share_token";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':share_token' => $shareToken]);
    
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

}
