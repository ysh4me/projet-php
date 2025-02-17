<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\GroupModel;
use App\Models\PhotoModel;

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
        $groups = $this->groupModel->getUserGroups($userId);
    
        $uniqueGroups = [];
        foreach ($groups as $group) {
            if (!isset($uniqueGroups[$group['id']])) { 
                $group['photos'] = $this->photoModel->getAlbumPhotos($group['id']); 
                $group['photo_count'] = $this->photoModel->countPhotosInAlbum($group['id']);
                $uniqueGroups[$group['id']] = $group;
            }
        }
    
        $groups = array_values($uniqueGroups);

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
            echo json_encode(["error" => "Vous devez être connecté pour partager un album."]);
            exit;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['album_id'])) {
            echo json_encode(["error" => "Album introuvable."]);
            exit;
        }
    
        $albumId = $data['album_id'];
    
        $existingShareToken = $this->groupModel->getExistingShareLink($albumId);
    
        if ($existingShareToken) {
            echo json_encode([
                "success" => "Lien existant retrouvé.",
                "share_url" => $_ENV['APP_URL'] . "/album/view/$existingShareToken"
            ]);
            exit;
        }
    
        $shareToken = bin2hex(random_bytes(16));
        $saved = $this->groupModel->saveShareLink($albumId, $shareToken);
    
        if ($saved) {
            echo json_encode([
                "success" => "Lien généré avec succès.",
                "share_url" => $_ENV['APP_URL'] . "/album/view?token=$shareToken"
            ]);
        } else {
            echo json_encode(["error" => "Erreur lors de la génération du lien de partage."]);
        }
    
        exit;
    }    

    public function viewSharedAlbum($token)
    {
        if (empty($token)) {
            die("Lien invalide.");
        }
    
        $album = $this->groupModel->getAlbumByToken($token);
    
        if (!$album) {
            die("Album introuvable ou lien expiré.");
        }
    
        $album['photos'] = $this->photoModel->getAlbumPhotos($album['id']);
    
        require_once '../app/Views/view_album.php';
    }
    
    


}
