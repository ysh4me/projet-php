<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/helpers.php';
?>

<?php include __DIR__ . '/../partials/head.php'; ?>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Inscription</h1>

        <?php if (!empty($_SESSION['error']['global'])): ?>
            <div class="error-message">
                <p><?= escape($_SESSION['error']['global']) ?></p>
            </div>
            <?php unset($_SESSION['error']['global']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form action="/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token'] ?? '') ?>">

            <label for="firstname">Prénom</label>
            <input type="text" id="firstname" name="firstname" value="<?= isset($_POST['firstname']) ? escape($_POST['firstname']) : '' ?>" required>
            <?php if (!empty($_SESSION['error']['firstname'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['firstname']) ?></p>
                <?php unset($_SESSION['error']['firstname']); ?>
            <?php endif; ?>

            <label for="lastname">Nom</label>
            <input type="text" id="lastname" name="lastname" value="<?= isset($_POST['lastname']) ? escape($_POST['lastname']) : '' ?>" required>
            <?php if (!empty($_SESSION['error']['lastname'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['lastname']) ?></p>
                <?php unset($_SESSION['error']['lastname']); ?>
            <?php endif; ?>

            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" value="<?= isset($_POST['username']) ? escape($_POST['username']) : '' ?>" required>
            <?php if (!empty($_SESSION['error']['username'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['username']) ?></p>
                <?php unset($_SESSION['error']['username']); ?>
            <?php endif; ?>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? escape($_POST['email']) : '' ?>" required>
            <?php if (!empty($_SESSION['error']['email'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['email']) ?></p>
                <?php unset($_SESSION['error']['email']); ?>
            <?php endif; ?>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <?php if (!empty($_SESSION['error']['password'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['password']) ?></p>
                <?php unset($_SESSION['error']['password']); ?>
            <?php endif; ?>

            <label for="passwordConfirm">Confirmer le mot de passe</label>
            <input type="password" id="passwordConfirm" name="passwordConfirm" required>
            <?php if (!empty($_SESSION['error']['passwordConfirm'])): ?>
                <p class="error-text"><?= escape($_SESSION['error']['passwordConfirm']) ?></p>
                <?php unset($_SESSION['error']['passwordConfirm']); ?>
            <?php endif; ?>

            <button type="submit">S'inscrire</button>

            <p>Déjà un compte ? <a href="/login">Connectez-vous ici</a></p>
        </form>
    </div>
</div>


<?php include __DIR__ . '/../partials/footer.php'; ?>
