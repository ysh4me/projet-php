<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/helpers.php';
?>

<?php include __DIR__ . '/../partials/head.php'; ?>

<div class="container">
    <h1>Connexion</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message">
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


        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? escape($_POST['email']) : '' ?>" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>

        <p><a href="/forgot-password">Mot de passe oublié ?</a></p>
    </form>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
