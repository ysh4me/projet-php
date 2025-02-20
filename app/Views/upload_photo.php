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
        <h1>Uploader une photo</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?= escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?= escape($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form action="/photo/upload" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

            <label for="photo">Sélectionnez une photo :</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/gif" required>

            <label for="group_id">Groupe :</label>
            <select id="group_id" name="group_id" required>
                <?php if (!empty($groups)): ?>
                    <option selected>Selectionner un groupe</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= escape($group['id']) ?>"><?= escape($group['name']) ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option disabled selected>Aucun groupe disponible</option>
                <?php endif; ?>
            </select>

            <button type="submit">Uploader</button>
        </form>
    </div>
</main>

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
