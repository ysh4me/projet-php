<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/Helpers.php';

$token = $_GET['token'] ?? '';
?>

<?php include __DIR__ . '/partials/head.php'; ?>

<?php include __DIR__ . '/partials/navbar.php'; ?>

<main>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Réinitialisation du mot de passe</h1>

            <?php if (!empty($_SESSION['error']['global'])): ?>
                <div class="error-message">
                    <p><?= escape($_SESSION['error']['global']) ?></p>
                </div>
                <?php unset($_SESSION['error']['global']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="success-message">
                    <p><?= escape($_SESSION['success']) ?></p>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form action="/update-password" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="token" value="<?= escape($token) ?>">

                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required>
                <?php if (!empty($_SESSION['error']['password'])): ?>
                    <p class="error-text"><?= escape($_SESSION['error']['password']) ?></p>
                    <?php unset($_SESSION['error']['password']); ?>
                <?php endif; ?>

                <label for="passwordConfirm">Confirmez le mot de passe</label>
                <input type="password" id="passwordConfirm" name="passwordConfirm" required>
                <?php if (!empty($_SESSION['error']['passwordConfirm'])): ?>
                    <p class="error-text"><?= escape($_SESSION['error']['passwordConfirm']) ?></p>
                    <?php unset($_SESSION['error']['passwordConfirm']); ?>
                <?php endif; ?>

                <button type="submit">Réinitialiser</button>
            </form>

            <p><a href="/login">Retour à la connexion</a></p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>