<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Galerie de photos</h1>
        <a href="/upload-photo" class="btn btn-primary">
            <i class="fas fa-upload"></i> Ajouter une photo
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="form-error mb-4">
            <p><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="form-success mb-4">
            <p><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
        </div>
    <?php endif; ?>

    <div class="filters-bar mb-4">
        <form method="GET" action="/photos" class="form-inline">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
            <div class="form-group">
                <label for="sort" class="mr-2">Trier par :</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Plus récent</option>
                    <option value="oldest" <?= ($sort === 'oldest') ? 'selected' : '' ?>>Plus ancien</option>
                    <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>Nom</option>
                </select>
                <button type="submit" class="btn btn-secondary ml-2">Appliquer</button>
            </div>
        </form>
    </div>

    <?php if (empty($photos)): ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>Aucune photo n'a été ajoutée pour le moment</p>
            <p>Cliquez sur le bouton "Ajouter une photo" ci-dessus pour commencer à créer votre galerie.</p>
        </div>
    <?php else: ?>
        <div class="photo-gallery">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item">
                    <div class="photo-wrapper">
                        <img src="/uploads/<?= escape($photo['filename']) ?>" alt="Photo" loading="lazy">
                        <div class="photo-overlay">
                            <h3><?= escape($photo['filename']) ?></h3>
                            <p class="upload-date"><?= date('d/m/Y', strtotime($photo['uploaded_at'])) ?></p>
                            <div class="photo-actions">
                                <form action="/photo/share" method="POST" class="d-inline">
                                    <input type="hidden" name="photo_id" value="<?= escape($photo['id']) ?>">
                                    <button type="submit" class="btn btn-primary btn-sm" title="Partager">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                </form>
                                <form action="/photo/delete" method="POST" class="d-inline">
                                    <input type="hidden" name="photo_id" value="<?= escape($photo['id']) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette photo ?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination-wrapper">
            <div class="btn-group">
                <?php if ($page > 1): ?>
                    <a href="/photos?page=<?= escape($page - 1) ?>&sort=<?= escape($sort) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </a>
                <?php endif; ?>

                <span class="btn btn-light">Page <?= escape($page) ?> sur <?= escape($totalPages) ?></span>

                <?php if ($page < $totalPages): ?>
                    <a href="/photos?page=<?= escape($page + 1) ?>&sort=<?= escape($sort) ?>" class="btn btn-outline-primary">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
