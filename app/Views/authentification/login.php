<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/Helpers.php';
?>

<?php include __DIR__ . '/../partials/head.php'; ?>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Connexion</h1>

        <?php if (!empty($_SESSION['error']['global'])): ?>
            <div class="error-message">
                <p><?= escape($_SESSION['error']['global']) ?></p>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token'] ?? '') ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= isset($_SESSION['old_input']['email']) ? escape($_SESSION['old_input']['email']) : '' ?>" required>
            <?php if (!empty($_SESSION['error']['email'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['email']) ?></p>
            <?php endif; ?>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <?php if (!empty($_SESSION['error']['password'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['password']) ?></p>
            <?php endif; ?>

            <button type="submit">Se connecter</button>

            <p><a href="/forgot-password">Mot de passe oublié ?</a></p>
        </form>
    </div>
</div>

<?php 
    unset($_SESSION['error']); 
    unset($_SESSION['old_input']);
?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
