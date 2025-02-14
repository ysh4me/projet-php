<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../Core/helpers.php';

    $token = $_GET['token'] ?? '';
?>

<?php include __DIR__ . '/partials/head.php'; ?>

<div class="container">
    <h1>Réinitialisation du mot de passe</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message"><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="/update-password" method="POST">
        <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

        <input type="hidden" name="token" value="<?= escape($token) ?>">

        <label for="password">Nouveau mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <label for="passwordConfirm">Confirmez le mot de passe :</label>
        <input type="password" id="passwordConfirm" name="passwordConfirm" required>

        <button type="submit">Réinitialiser</button>
    </form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
