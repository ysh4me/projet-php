<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\GroupModel;

class GroupController extends Controller
{
    private GroupModel $groupModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close(); 
        }
    
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $this->groupModel = new GroupModel();
    }

    public function index()
    {
        $_SESSION['user_id'] = "550e8400-e29b-41d4-a716-446655440000"; 

        $groups = $this->groupModel->getUserGroups($_SESSION['user_id']);
        require_once '../app/Views/groups.php';
    }

    public function createGroup()
    {        
        if (!isset($_POST['group_name']) || empty(trim($_POST['group_name']))) {
            $_SESSION['error'] = "Le nom du groupe est requis.";
            header("Location: /groups");
            exit;
        }

        $groupName = trim($_POST['group_name']);
        $userId = $_SESSION['user_id'];

        $groupId = $this->groupModel->createGroup($groupName, $userId);

        if ($groupId) {
            $_SESSION['success'] = "Groupe créé avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la création du groupe.";
        }

        header("Location: /groups");
        exit;
    }

    public function deleteGroup()
    {
        if (!isset($_POST['group_id'])) {
            $_SESSION['error'] = "Groupe introuvable.";
            header("Location: /groups");
            exit;
        }

        $groupId = $_POST['group_id'];
        $userId = $_SESSION['user_id'];

        if ($this->groupModel->deleteGroup($groupId, $userId)) {
            $_SESSION['success'] = "Groupe supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer ce groupe.";
        }

        header("Location: /groups");
        exit;
    }
}
