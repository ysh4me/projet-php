<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/helpers.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    $_SESSION['error'] = "Vous devez être connecté pour uploader une photo.";
    header("Location: /login");
    exit;
}

// Récupérer la liste des groupes de l'utilisateur
$photoModel = new \App\Models\PhotoModel();
$groups = $photoModel->getUserGroups($_SESSION['user']['id']);
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Ajouter une photo</h1>
        <a href="/photos" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la galerie
        </a>
    </div>

    <div class="upload-photo">
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

        <form action="/photo/upload" method="POST" enctype="multipart/form-data" class="form-container">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token'] ?? '') ?>">
            
            <?php if (empty($groups)): ?>
                <div class="form-error mb-4">
                    <p>Vous devez d'abord créer un groupe avant de pouvoir ajouter des photos.</p>
                    <a href="/groups" class="btn btn-primary mt-2">Créer un groupe</a>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="group_id">Sélectionner un groupe</label>
                    <select name="group_id" id="group_id" class="form-control" required>
                        <option value="">Choisir un groupe...</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?= escape($group['id']) ?>"><?= escape($group['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="photo">Sélectionner une photo</label>
                    <input type="file" id="photo" name="photo" accept="image/*" required class="form-control" onchange="previewImage(this)">
                </div>

                <div class="form-group">
                    <div id="preview-container" style="display: none;" class="text-center">
                        <img id="preview" src="" alt="Aperçu" class="preview-image">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-upload"></i> Télécharger
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const container = document.getElementById('preview-container');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
