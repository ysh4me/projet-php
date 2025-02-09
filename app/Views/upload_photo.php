<?php include __DIR__ . '/../partials/head.php'; ?>

<div class="container">
    <h1>Uploader une photo</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error-message"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success-message"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <form action="/photo/upload" method="POST" enctype="multipart/form-data">
        <label for="photo">Sélectionnez une photo :</label>
        <input type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/gif" required>

        <!-- Sélection du groupe -->
        <label for="group_id">Sélectionnez un groupe :</label>
        <select id="group_id" name="group_id" required>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Uploader</button>
    </form>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
