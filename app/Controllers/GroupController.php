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
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
            header("Location: /login");
            exit;
        }

        $groups = $this->groupModel->getUserGroups($_SESSION['user']['id']);
        require_once '../app/Views/groups.php';
    }

    public function createGroup()
    {        
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour créer un groupe.";
            header("Location: /login");
            exit;
        }

        if (!isset($_POST['group_name']) || empty(trim($_POST['group_name']))) {
            $_SESSION['error'] = "Le nom du groupe est requis.";
            header("Location: /groups");
            exit;
        }

        $groupName = trim($_POST['group_name']);
        $userId = $_SESSION['user']['id'];

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
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour supprimer un groupe.";
            header("Location: /login");
            exit;
        }

        if (!isset($_POST['group_id'])) {
            $_SESSION['error'] = "Groupe introuvable.";
            header("Location: /groups");
            exit;
        }

        $groupId = $_POST['group_id'];
        $userId = $_SESSION['user']['id'];

        if ($this->groupModel->deleteGroup($groupId, $userId)) {
            $_SESSION['success'] = "Groupe supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer ce groupe.";
        }

        header("Location: /groups");
        exit;
    }
}
