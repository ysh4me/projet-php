<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>

<div class="container">
    <h1>Mot de passe oublié</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message"><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php else: ?>
        <form action="/forgot-password" method="POST">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <button type="submit">Envoyer le lien</button>
        </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
