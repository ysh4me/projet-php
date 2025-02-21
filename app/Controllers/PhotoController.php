<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\PhotoModel;
use App\Models\GroupModel;

class PhotoController extends Controller
{
    private PhotoModel $photoModel;
    private GroupModel $groupModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close(); 
        }
    
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $this->photoModel = new PhotoModel();
        $this->groupModel = new GroupModel();
    }

    public function uploadPhoto()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            echo json_encode(["error" => "Vous devez être connecté pour uploader des photos."]);
            exit;
        }

        if (!isset($_POST['group_id']) || empty($_POST['group_id'])) {
            echo json_encode(["error" => "Veuillez sélectionner un album."]);
            exit;
        }

        if (!isset($_FILES['photos']) || count($_FILES['photos']['name']) === 0) {
            echo json_encode(["error" => "Aucune photo sélectionnée."]);
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/heic'];
        $maxSize = 5 * 1024 * 1024; 
        $uploadDir = __DIR__ . '/../../public/uploads/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadedPhotos = [];
        foreach ($_FILES['photos']['name'] as $key => $name) {
            $fileType = $_FILES['photos']['type'][$key];
            $fileSize = $_FILES['photos']['size'][$key];
            $tmpName = $_FILES['photos']['tmp_name'][$key];
            $error = $_FILES['photos']['error'][$key];

            if ($error !== UPLOAD_ERR_OK) {
                continue; 
            }

            if (!in_array($fileType, $allowedTypes)) {
                continue; 
            }

            if ($fileSize > $maxSize) {
                continue;
            }

            $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
            $fileName = uniqid('photo_', true) . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $filePath)) {
                $this->photoModel->savePhoto($_SESSION['user']['id'], $_POST['group_id'], $fileName, $fileType, $fileSize);
                $uploadedPhotos[] = $fileName;
            }
        }

        if (!empty($uploadedPhotos)) {
            echo json_encode([
                "success" => "Photos uploadées avec succès.",
                "photos" => $uploadedPhotos
            ]);
        } else {
            echo json_encode(["error" => "Aucune photo n'a été uploadée."]);
        }

        exit;
    }

    public function showPhotos()
    {
        $userId = $_SESSION['user']['id'] ?? null;

        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10; 
        $offset = ($page - 1) * $limit;

        $photos = $this->photoModel->getUserPhotos($userId, $sort, $limit, $offset);
        $totalPhotos = $this->photoModel->countUserPhotos($userId);
        $totalPages = ceil($totalPhotos / $limit);

        require_once '../app/Views/photos.php';
    }

    public function deletePhoto()
    {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            echo json_encode(["error" => "Vous devez être connecté pour supprimer une photo."]);
            exit;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['photo_id'])) {
            echo json_encode(["error" => "Photo introuvable."]);
            exit;
        }
    
        $photoId = $data['photo_id'];
        $deleted = $this->photoModel->deletePhoto($photoId, $_SESSION['user']['id']);
    
        if ($deleted) {
            echo json_encode(["success" => "Photo supprimée avec succès."]);
        } else {
            echo json_encode(["error" => "Erreur lors de la suppression de la photo."]);
        }
    
        exit;
    }
    
    

    public function generateShareLink()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour générer un lien de partage.";
            header("Location: /login");
            exit;
        }

        if (!isset($_POST['photo_id']) || empty($_POST['photo_id'])) {
            $_SESSION['error'] = "Photo introuvable.";
            header("Location: /photos");
            exit;
        }

        $photoId = $_POST['photo_id'];
        $userId = $_SESSION['user']['id'];

        $photo = $this->photoModel->getPhotoById($photoId);

        if (!$photo || $photo['user_id'] !== $userId) {
            $_SESSION['error'] = "Vous ne pouvez partager que vos propres photos.";
            header("Location: /photos");
            exit;
        }

        $shareToken = bin2hex(random_bytes(16));

        $this->photoModel->saveShareLink($photoId, $shareToken);

        $_SESSION['success'] = "Lien de partage généré : " . $_ENV['APP_URL'] . "/photo/view?token=$shareToken";

        header("Location: /photos");
        exit;
    }

    public function viewPhoto()
    {
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            die("Lien invalide.");
        }

        $shareToken = $_GET['token'];

        $photo = $this->photoModel->getPhotoByToken($shareToken);

        if (!$photo) {
            die("Photo introuvable ou lien expiré.");
        }

        require_once '../app/Views/view_photo.php';
    }
}
