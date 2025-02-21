<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/Helpers.php';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main>
        <section class="albums">
            <div class="container">
                <div class="albums-header">
                    <h1>Albums</h1>
                    <div class="actions">
                        <button id="openModalBtn" class="btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/>
                            </svg>
                            Créer un album
                        </button>

                    </div>
                </div>

                <div class="filters">
                    <button class="filter-btn active" data-filter="all">Tous les éléments</button>
                    <button class="filter-btn" data-filter="shared-by-me">Mes albums partagés</button>
                    <button class="filter-btn" data-filter="shared-with-me">Éléments partagés avec moi</button>
                </div>


                <div class="albums-grid">
                    <?php foreach ($groups as $group): ?>
                        <div class="album-card" 
                            data-group-id="<?= escape($group['id']) ?>"
                            data-group-name="<?= escape($group['name']) ?>"
                            data-is-shared="<?= isset($group['is_shared']) ? (int) $group['is_shared'] : 0 ?>"
                            data-shared-with-me="<?= isset($group['shared_with_me']) ? (int) $group['shared_with_me'] : 0 ?>"
                            data-photos='<?= json_encode(array_column($group["photos"], "filename")) ?>'>


                            <div class="album-header">
                                <h2><?= escape($group['name']) ?></h2>
                                <div class="album-options">
                                    <button class="options-btn">⋮</button>
                                    <div class="options-menu">
                                        <button class="edit-album" data-group-id="<?= escape($group['id']) ?>">Modifier</button>
                                        <button class="share-album" data-group-id="<?= escape($group['id']) ?>">Partager</button>
                                        <button class="delete-album" data-group-id="<?= escape($group['id']) ?>">Supprimer</button>
                                    </div>
                                </div>
                            </div>

                            <div class="album-preview">
                                <?php if (!empty($group['photos'])): ?>
                                    <?php foreach ($group['photos'] as $photo): ?>
                                        <div class="photo-square">
                                            <img src="/uploads/<?= escape($photo['filename']) ?>" alt="Photo de l'album">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="photo-square placeholder">
                                        <img src="/images/placeholder.jpg" alt="Aucune photo">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <p><?= $group['photo_count'] ?> élément(s)</p>

                            <?php if (!empty($group['is_shared'])): ?>
                                <div class="shared-icon visible" title="Album partagé">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a3 3 0 1 0 6 0a3 3 0 1 0-6 0m12-6a3 3 0 1 0 6 0a3 3 0 1 0-6 0m0 12a3 3 0 1 0 6 0a3 3 0 1 0-6 0m-6.3-7.3l6.6-3.4m-6.6 6l6.6 3.4" />
                                </svg>
                                </div>
                            <?php endif; ?>

                        </div>


                    <?php endforeach; ?>
                </div>

            </div>
        </section>
    </main>

    <!-- modals -->
    <div id="modalAlbum" class="modal">
        <div class="modal-content">
            <div class="albums-header-modal">
                <h1>Créer un album</h1>
                <span class="close-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144m224 0L144 368" />
                    </svg>
                </span>
            </div>
            <form action="/album/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
                
                <label for="album_name">Nom de l'album</label>
                <input type="text" id="album_name" name="album_name" required>

                <button type="submit" class="btn-primary">Créer</button>
            </form>
        </div>
    </div>

    <div id="albumModal" class="modal">
        <div class="modal-content">
            <div class="albums-header-modal">
                <h1 id="modalTitle"></h1>
                <span class="close-modal" id="closeAlbumModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144m224 0L144 368" />
                    </svg>
                </span>
            </div>

            <form id="uploadPhotoForm" action="/photo/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="group_id" id="uploadAlbumId">

                <label for="photos">Ajouter des photos</label>
                <input type="file" id="photos" name="photos[]" accept="image/*" multiple required>
                <button type="submit" class="btn-primary">Uploader</button>
            </form>


            <div id="modalPhotos" class="modal-photos"></div>
        </div>
    </div>

    <div id="modalConfirmDelete" class="modal">
        <div class="modal-content">
            <div class="albums-header-modal">
                <h1>Supprimer l'album</h1>
                <span class="close-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144m224 0L144 368"/>
                    </svg>
                </span>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet album ? Cette action est irréversible et supprimera les photos de votre album.</p>
                <input type="hidden" id="deleteAlbumId">
            </div>
            <div class="modal-actions">
                <button class="btn-secondary close-modal">Annuler</button>
                <button id="confirmDeleteAlbum" class="btn-danger">Supprimer</button>
            </div>
        </div>
    </div>

    <div id="modalEditAlbum" class="modal">
        <div class="modal-content">
            <div class="albums-header-modal">
                <h1>Modifier l'album</h1>
                <span class="close-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144m224 0L144 368"/>
                    </svg>
                </span>
            </div>
            <div class="modal-body">
                <label for="newAlbumName">Nouveau nom</label>
                <input type="text" id="newAlbumName" placeholder="Entrez le nouveau nom de l'album">
                <input type="hidden" id="editAlbumId">
            </div>
            <div class="modal-actions">
                <button class="btn-secondary close-modal">Annuler</button>
                <button id="confirmEditAlbum" class="btn-primary">Modifier</button>  
            </div>
        </div>
    </div>

    <div id="modalShareAlbum" class="modal">
        <div class="modal-content">
            <div class="albums-header-modal">
                <h1>Lien de partage</h1>
                <span class="close-modal" id="closeShareModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144m224 0L144 368"/>
                    </svg>
                </span>
            </div>
            <div class="modal-body">
                <p>Copiez ce lien pour partager l'album</p>
                <input type="text" id="shareLink" readonly>
                <p id="permissionInfo"></p>
                <button id="copyShareLink" class="btn-primary">Copier</button>

                <label class="checkbox-container">
                    <input type="checkbox" id="toggleUploadPermission" data-album-id="" >
                    <span class="checkmark"></span>
                    Autoriser l'ajout de photos
                </label>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>

</body>
