<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/Helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<main>
    <div class="container">
        <h1>Galerie de photos</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form method="GET" action="/photos">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

            <label for="sort">Trier par :</label>
            <select name="sort" id="sort">
                <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Date (du plus récent au plus ancien)</option>
                <option value="oldest" <?= ($sort === 'oldest') ? 'selected' : '' ?>>Date (du plus ancien au plus récent)</option>
                <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>Nom de fichier</option>
            </select>
            <button type="submit">Trier</button>
        </form>

        <div class="gallery">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item">
                    <img src="/uploads/<?= escape($photo['filename']) ?>" alt="Photo">
                    <p><?= escape($photo['filename']) ?> - <?= date('d/m/Y', strtotime($photo['uploaded_at'])) ?></p>

                    <form action="/photo/delete" method="POST">
                        <input type="hidden" name="photo_id" value="<?= escape($photo['id']) ?>">
                        <button type="submit">Supprimer</button>
                    </form>

                    <form action="/photo/share" method="POST">
                        <input type="hidden" name="photo_id" value="<?= escape($photo['id']) ?>">
                        <button type="submit">Générer un lien</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="/photos?page=<?= escape($page - 1) ?>&sort=<?= escape($sort) ?>">← Précédent</a>
            <?php endif; ?>

            <span>Page <?= escape($page) ?> sur <?= escape($totalPages) ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="/photos?page=<?= escape($page + 1) ?>&sort=<?= escape($sort) ?>">Suivant →</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
