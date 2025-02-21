<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\GroupModel;
use App\Models\PhotoModel;
use PDO;

class GroupController extends Controller
{
    private GroupModel $groupModel;
    private PhotoModel $photoModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close(); 
        }
    
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $this->groupModel = new GroupModel();
        $this->photoModel = new PhotoModel();
    }

    public function index()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour voir vos groupes.";
            header("Location: /login");
            exit;
        }
    
        $userId = $_SESSION['user']['id'];
        $groups = $this->groupModel->getAlbums($userId); 
    
        foreach ($groups as &$group) {
            $group['photos'] = $this->photoModel->getAlbumPhotos($group['id']); 
            $group['photo_count'] = $this->photoModel->countPhotosInAlbum($group['id']);
        }
    
        require_once '../app/Views/groups.php';
    }
    
    

    public function createGroup()
    {    
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Erreur : Vous devez être connecté pour créer un groupe.";
            header("Location: /login");
            exit;
        }
    
        $userId = $_SESSION['user']['id'];

    
        if (!isset($_POST['album_name']) || empty(trim($_POST['album_name']))) {
            $_SESSION['error'] = "Le nom de l'album est requis.";
            header("Location: /albums");
            exit;
        }

        $groupName = trim($_POST['album_name']);
        
    
        try {
            $groupId = $this->groupModel->createGroup($groupName, $userId);
    
            if ($groupId) {
                $_SESSION['success'] = "Groupe créé avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la création du groupe.";
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }
    
        header("Location: /albums");
        exit;
    }    

    public function updateAlbum()
    {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            echo json_encode(["error" => "Vous devez être connecté pour modifier un album."]);
            exit;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['album_id']) || !isset($data['new_name'])) {
            echo json_encode(["error" => "Données invalides.", "received" => $data]);
            exit;
        }
    
        $albumId = $data['album_id'];
        $newName = trim($data['new_name']);
    
        if (empty($newName)) {
            echo json_encode(["error" => "Le nom ne peut pas être vide."]);
            exit;
        }
    
        $updated = $this->groupModel->updateGroupName($albumId, $newName);
    
        if ($updated) {
            echo json_encode(["success" => "Album modifié avec succès."]);
        } else {
            echo json_encode(["error" => "Erreur lors de la modification de l'album.", "album_id" => $albumId, "new_name" => $newName]);
        }
    
        exit;
    }

    public function deleteAlbum()
    {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            echo json_encode(["error" => "Vous devez être connecté pour supprimer un album."]);
            exit;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['album_id'])) {
            echo json_encode(["error" => "Album introuvable."]);
            exit;
        }
    
        $albumId = $data['album_id'];
        $deleted = $this->groupModel->deleteGroup($albumId, $_SESSION['user']['id']);
    
        if ($deleted) {
            echo json_encode(["success" => "Album supprimé avec succès."]);
        } else {
            echo json_encode(["error" => "Erreur lors de la suppression."]);
        }
    
        exit;
    }

    public function generateShareLink()
    {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            http_response_code(403);
            echo json_encode(["error" => "Vous devez être connecté pour partager un album."]);
            exit;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['album_id']) || !isset($data['permission'])) {
            http_response_code(400);
            echo json_encode(["error" => "Album introuvable ou permission non définie."]);
            exit;
        }
    
        $albumId = $data['album_id'];
        $permission = in_array($data['permission'], ['read_only', 'can_upload']) ? $data['permission'] : 'read_only';
    
        $query = "SELECT id FROM `groups` WHERE id = :album_id";
        $stmt = $this->groupModel->getPdo()->prepare($query);
        $stmt->execute([':album_id' => $albumId]);
    
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(["error" => "Album introuvable."]);
            exit;
        }
    
        $existingToken = $this->groupModel->getExistingShareLink($albumId);
        
        if ($existingToken) {
            echo json_encode([
                "success" => "Lien déjà existant.",
                "share_url" => $_ENV['APP_URL'] . "/album/view?token=$existingToken",
                "permission" => $permission
            ]);
            exit;
        }
    
        $shareToken = bin2hex(random_bytes(16));
        $saved = $this->groupModel->saveShareLink($albumId, $shareToken, $permission);
    
        if ($saved) {
            echo json_encode([
                "success" => "Lien généré avec succès.",
                "share_url" => $_ENV['APP_URL'] . "/album/view/$shareToken",
                "permission" => $permission
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la génération du lien de partage."]);
        }
    
        exit;
    }

    public function updateSharePermission()
    {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            http_response_code(403);
            echo json_encode(["error" => "Vous devez être connecté pour modifier la permission."]);
            exit;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['album_id']) || !isset($data['permission'])) {
            http_response_code(400);
            echo json_encode(["error" => "Album ID ou permission non définis."]);
            exit;
        }
    
        $albumId = $data['album_id'];
        $newPermission = in_array($data['permission'], ['read_only', 'can_upload']) ? $data['permission'] : 'read_only';
    
        $existingToken = $this->groupModel->getExistingShareLink($albumId);
    
        if (!$existingToken) {
            http_response_code(404);
            echo json_encode(["error" => "Album introuvable ou non partagé."]);
            exit;
        }
    
        $updated = $this->groupModel->saveShareLink($albumId, $existingToken, $newPermission);
    
        if ($updated) {
            echo json_encode(["success" => "Permission mise à jour.", "new_permission" => $newPermission]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la mise à jour de la permission."]);
        }
    
        exit;
    }

    public function getSharePermission()
    {
        header('Content-Type: application/json');
    
        if (!isset($_GET['album_id']) || empty($_GET['album_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Album ID manquant."]);
            exit;
        }
    
        $albumId = $_GET['album_id'];
    
        $query = "SELECT permission, share_token FROM album_shares WHERE album_id = :album_id LIMIT 1";
        $stmt = $this->groupModel->getPdo()->prepare($query);
        $stmt->execute([':album_id' => $albumId]);
        $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$album) {
            http_response_code(404);
            echo json_encode(["error" => "Album non trouvé ou non partagé."]);
            exit;
        }
    
        echo json_encode([
            "success" => true,
            "permission" => $album['permission'],
            "share_url" => $_ENV['APP_URL'] . "/album/view?token=" . $album['share_token']
        ]);
        exit;
    }
    

    
    public function viewSharedAlbum()
    {
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            die("Lien invalide.");
        }
    
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour voir un album partagé.";
            header("Location: /login");
            exit;
        }
    
        $token = $_GET['token'];
        $album = $this->groupModel->getAlbumByToken($token);
    
        if (!$album) {
            die("Album introuvable ou lien expiré.");
        }
    
        $album['photos'] = $this->photoModel->getAlbumPhotos($album['id']);
        $permission = $album['permission']; 
    
        require_once '../app/Views/view_album.php';
    }

}
