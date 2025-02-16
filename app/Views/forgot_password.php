<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>

<div class="container">
    <div class="auth-page">
        <div class="form-container">
            <h2>Mot de passe oublié</h2>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="form-error"><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="form-success"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php else: ?>
                <form action="/forgot-password" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Envoyer le lien</button>

                    <div class="form-footer">
                        <a href="/login" class="btn-link">Retour à la connexion</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
