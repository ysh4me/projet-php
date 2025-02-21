<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/Helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<main class="confirmation-container">
    <div class="confirmation-card">
        <h1>Email Vérifié !</h1>
        <p class="confirmation-text">
            Félicitations ! Votre email a été confirmé avec succès. Vous pouvez maintenant vous connecter et profiter de notre plateforme.
        </p>
        <div class="confirmation-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
            <g fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0-18 0" />
                <path d="m9 12l2 2l4-4" />
            </g>
        </svg>
        </div>
        <a href="/login" class="btn btn-primary">Se connecter</a>
    </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
