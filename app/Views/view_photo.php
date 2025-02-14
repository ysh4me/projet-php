<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/Core/helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="container">
    <h1>Photo partagée</h1>

    <div class="photo-item">
        <img src="/uploads/<?= escape($photo['filename']) ?>" alt="Photo">
        <p>Publié le : <?= escape(date('d/m/Y', strtotime($photo['uploaded_at']))) ?></p>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
