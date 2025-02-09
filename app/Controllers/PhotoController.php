<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\PhotoModel;

class PhotoController extends Controller
{
    public function uploadPhoto()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour uploader une photo.";
            header("Location: /login");
            exit;
        }

        if (!isset($_POST['group_id']) || empty($_POST['group_id'])) {
            $_SESSION['error'] = "Veuillez sélectionner un groupe.";
            header("Location: /photo/upload");
            exit;
        }

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Veuillez sélectionner une image valide.";
            header("Location: /photo/upload");
            exit;
        }

        $file = $_FILES['photo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5 Mo

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = "Seuls les fichiers JPG, PNG et GIF sont autorisés.";
            header("Location: /photo/upload");
            exit;
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "La taille maximale est de 5 Mo.";
            header("Location: /photo/upload");
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
            header("Location: /photo/upload");
            exit;
        }

        $photoModel = new PhotoModel();
        $saved = $photoModel->savePhoto(
            (string) $_SESSION['user_id'],  
            (string) $_POST['group_id'],
            $fileName,
            $file['type'],
            $file['size']
        );

        if ($saved) {
            $_SESSION['success'] = "Votre photo a bien été uploadée.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement en base de données.";
        }

        header("Location: /photo/upload");
        exit;
    }
}
