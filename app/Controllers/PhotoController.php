<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\PhotoModel;

class PhotoController extends Controller
{
    private PhotoModel $photoModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close(); 
        }
    
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $this->photoModel = new PhotoModel();
    }

    public function uploadPhoto()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour uploader une photo.";
            header("Location: /login");
            exit;
        }

        if (!isset($_POST['group_id']) || empty($_POST['group_id'])) {
            $_SESSION['error'] = "Veuillez sélectionner un groupe.";
            header("Location: /upload-photo");
            exit;
        }

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Veuillez sélectionner une image valide.";
            header("Location: /upload-photo");
            exit;
        }

        $file = $_FILES['photo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5 Mo

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = "Seuls les fichiers JPG, PNG et GIF sont autorisés.";
            header("Location: /upload-photo");
            exit;
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "La taille maximale est de 5 Mo.";
            header("Location: /upload-photo");
            exit;
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('photo_', true) . '.' . $fileExtension;

        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            $_SESSION['error'] = "Erreur lors du téléchargement du fichier.";
            header("Location: /upload-photo");
            exit;
        }

        $saved = $this->photoModel->savePhoto(
            $_SESSION['user']['id'],
            $_POST['group_id'],
            $fileName,
            $file['type'],
            $file['size']
        );

        if ($saved) {
            $_SESSION['success'] = "Votre photo a bien été uploadée.";
            header("Location: /photos");
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement en base de données.";
            header("Location: /upload-photo");
        }
        exit;
    }

    public function showPhotos()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour voir vos photos.";
            header("Location: /login");
            exit;
        }

        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10; 
        $offset = ($page - 1) * $limit;

        $photos = $this->photoModel->getUserPhotos($_SESSION['user']['id'], $sort, $limit, $offset);
        $totalPhotos = $this->photoModel->countUserPhotos($_SESSION['user']['id']);
        $totalPages = ceil($totalPhotos / $limit);

        require_once '../app/Views/photos.php';
    }

    public function deletePhoto()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour supprimer une photo.";
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
            $_SESSION['error'] = "Vous ne pouvez supprimer que vos propres photos.";
            header("Location: /photos");
            exit;
        }

        $filePath = __DIR__ . "/../../public/uploads/" . $photo['filename'];

        if (file_exists($filePath)) {
            unlink($filePath); 
        }

        $deleted = $this->photoModel->deletePhoto($photoId);

        if ($deleted) {
            $_SESSION['success'] = "Photo supprimée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de la photo.";
        }

        header("Location: /photos");
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
