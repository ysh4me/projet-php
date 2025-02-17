<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/helpers.php';
?>

<?php include __DIR__ . '/../partials/head.php'; ?>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="container">
    <div class="auth-page">
        <div class="form-container">
            <h2>Connexion</h2>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="form-error">
                    <ul>
                        <?php foreach ($_SESSION['error'] as $error): ?>
                            <li><?= escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token'] ?? '') ?>">

                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? escape($_POST['email']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>

                <div class="form-footer">
                    <a href="/forgot-password" class="btn-link">Mot de passe oublié ?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
