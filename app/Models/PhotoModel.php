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
    
    public function getAlbumPhotos(string $albumId, int $limit = 4): array
    {
        $query = "SELECT filename FROM photos WHERE group_id = :album_id ORDER BY uploaded_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':album_id', $albumId, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPhotosInAlbum(string $albumId): int
    {
        $query = "SELECT COUNT(*) FROM photos WHERE group_id = :album_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':album_id' => $albumId]);
        return (int) $stmt->fetchColumn();
    }

    public function getUserGroups(string $userId): array
    {
        $query = "SELECT g.id, g.name 
                FROM group_members gm
                JOIN `groups` g ON gm.group_id = g.id
                WHERE gm.user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserPhotos(string $userId, string $sort, int $limit, int $offset): array
    {
        $orderBy = match ($sort) {
            'newest' => 'uploaded_at DESC',
            'oldest' => 'uploaded_at ASC',
            'name' => 'filename ASC',
            default => 'uploaded_at DESC'
        };

        $query = "SELECT * FROM photos WHERE user_id = :user_id ORDER BY $orderBy LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUserPhotos(string $userId): int
    {
        $query = "SELECT COUNT(*) FROM photos WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getPhotoById(string $photoId): ?array
    {
        $query = "SELECT * FROM photos WHERE id = :photo_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':photo_id' => $photoId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function deletePhoto(string $photoId, string $userId): bool
    {
        $query = "DELETE FROM photos WHERE id = :photo_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':photo_id' => $photoId,
            ':user_id' => $userId
        ]);
    }
    
    
    public function saveShareLink(string $photoId, string $shareToken): bool
    {
        $query = "INSERT INTO photo_shares (id, photo_id, share_token, expires_at) 
                VALUES (UUID(), :photo_id, :share_token, NULL)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':photo_id' => $photoId,
            ':share_token' => $shareToken
        ]);
    }

    public function getPhotoByToken(string $shareToken): ?array
    {
        $query = "SELECT p.* FROM photo_shares ps 
                JOIN photos p ON ps.photo_id = p.id 
                WHERE ps.share_token = :share_token";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':share_token' => $shareToken]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }



}
