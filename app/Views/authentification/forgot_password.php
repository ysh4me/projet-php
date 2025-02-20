<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/Helpers.php';
?>

<?php include __DIR__ . '/../partials/head.php'; ?>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<main>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Mot de passe oublié</h1>

            <?php if (!empty($_SESSION['error']['global'])): ?>
                <div class="error-message">
                    <p><?= escape($_SESSION['error']['global']) ?></p>
                </div>
                <?php unset($_SESSION['error']['global']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <p class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
            <?php else: ?>
                <form action="/forgot-password" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token'] ?? '') ?>">

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? escape($_POST['email']) : '' ?>" required>
                    <?php if (!empty($_SESSION['error']['email'])): ?>
                        <p class="error-text"><?= escape($_SESSION['error']['email']) ?></p>
                        <?php unset($_SESSION['error']['email']); ?>
                    <?php endif; ?>

                    <button type="submit">Envoyer le lien</button>

                    <p><a href="/login">Retour à la connexion</a></p>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
