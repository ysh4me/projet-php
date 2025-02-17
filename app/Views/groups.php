<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="container">
    <div class="groups-container">
        <h1>Gestion des groupes</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <div class="create-group">
            <h2>Créer un groupe</h2>
            <form action="/group/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

                <div class="form-group">
                    <label for="group_name">Nom du groupe :</label>
                    <input type="text" id="group_name" name="group_name" required>
                </div>
                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        </div>

        <div class="groups-list">
            <h2>Vos groupes</h2>
            <?php if (empty($groups)): ?>
                <div class="empty-groups">
                    <i class="fas fa-users"></i>
                    <p>Vous n'avez pas encore de groupes</p>
                </div>
            <?php else: ?>
                <ul>
                    <?php foreach ($groups as $group): ?>
                        <li>
                            <span class="group-name"><?= escape($group['name']) ?></span>
                            <div class="group-actions">
                                <a href="/group/manage?id=<?= escape($group['id']) ?>" class="btn btn-primary">Gérer</a>
                                <?php if ($group['owner_id'] === $_SESSION['user_id']): ?>
                                    <form action="/group/delete" method="POST">
                                        <input type="hidden" name="group_id" value="<?= escape($group['id']) ?>">
                                        <button type="submit" class="btn btn-danger delete-btn" onclick="return confirm('Voulez-vous vraiment supprimer ce groupe ?');">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
