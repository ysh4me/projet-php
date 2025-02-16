<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/helpers.php';
?>

<?php include __DIR__ . '/../partials/head.php'; ?>

<div class="container">
    <div class="auth-page">
        <div class="form-container">
            <h2>Inscription</h2>

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

            <?php if (isset($_SESSION['success'])): ?>
                <p class="form-success"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
            <?php endif; ?>

            <form action="/register" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token'] ?? '') ?>">

                <div class="form-group">
                    <label for="firstname">Prénom :</label>
                    <input type="text" id="firstname" name="firstname" value="<?= isset($_POST['firstname']) ? escape($_POST['firstname']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="lastname">Nom :</label>
                    <input type="text" id="lastname" name="lastname" value="<?= isset($_POST['lastname']) ? escape($_POST['lastname']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" value="<?= isset($_POST['username']) ? escape($_POST['username']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? escape($_POST['email']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="passwordConfirm">Confirmer le mot de passe :</label>
                    <input type="password" id="passwordConfirm" name="passwordConfirm" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>

                <div class="form-footer">
                    <a href="/login" class="btn-link">Déjà inscrit ? Se connecter</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
