<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/Helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="container">
    <h1>Album partagé : <?= escape($album['name']) ?></h1>

    <?php if (!empty($album['photos'])): ?>
        <div class="album-preview">
            <?php foreach ($album['photos'] as $photo): ?>
                <div class="photo-square">
                    <img src="/uploads/<?= escape($photo['filename']) ?>" alt="Photo">
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Cet album ne contient aucune photo.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
