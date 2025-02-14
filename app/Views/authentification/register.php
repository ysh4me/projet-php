<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/helpers.php';
?>

<?php include __DIR__ . '/../partials/head.php'; ?>

<div class="container">
    <h1>Inscription</h1>

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

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <form action="/register" method="POST">
        <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token'] ?? '') ?>">

        <label for="firstname">Prénom :</label>
        <input type="text" id="firstname" name="firstname" value="<?= isset($_POST['firstname']) ? escape($_POST['firstname']) : '' ?>" required>

        <label for="lastname">Nom :</label>
        <input type="text" id="lastname" name="lastname" value="<?= isset($_POST['lastname']) ? escape($_POST['lastname']) : '' ?>" required>

        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" value="<?= isset($_POST['username']) ? escape($_POST['username']) : '' ?>" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? escape($_POST['email']) : '' ?>" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <label for="passwordConfirm">Confirmer le mot de passe :</label>
        <input type="password" id="passwordConfirm" name="passwordConfirm" required>

        <button type="submit">S'inscrire</button>
    </form>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
