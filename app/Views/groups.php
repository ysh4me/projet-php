<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/Core/helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="container">
    <h1>Gestion des groupes</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error-message"><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <h2>Créer un groupe</h2>
    <form action="/group/create" method="POST">
        <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

        <label for="group_name">Nom du groupe :</label>
        <input type="text" id="group_name" name="group_name" required>
        <button type="submit">Créer</button>
    </form>

    <h2>Vos groupes</h2>
    <ul>
        <?php foreach ($groups as $group): ?>
            <li>
                <?= escape($group['name']) ?>
                <a href="/group/manage?id=<?= escape($group['id']) ?>">Gérer</a>
                <?php if ($group['owner_id'] === $_SESSION['user_id']): ?>
                    <form action="/group/delete" method="POST" style="display:inline;">
                        <input type="hidden" name="group_id" value="<?= escape($group['id']) ?>">
                        <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer ce groupe ?');">Supprimer</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
