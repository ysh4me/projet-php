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

    <?php if ($album['permission'] === 'can_upload'): ?>
        <h2>Ajouter des photos</h2>
        <form action="/photo/upload" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="group_id" value="<?= escape($album['id']) ?>">

            <label for="photos">Sélectionnez des photos :</label>
            <input type="file" id="photos" name="photos[]" accept="image/*" multiple required>
            <button type="submit" class="btn-primary">Uploader</button>
        </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
