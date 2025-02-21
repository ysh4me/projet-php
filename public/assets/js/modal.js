document.addEventListener("DOMContentLoaded", function () {
    const modalAlbum = document.getElementById("modalAlbum");
    const albumModal = document.getElementById("albumModal");
    const modalDelete = document.getElementById("modalConfirmDelete");
    const modalEdit = document.getElementById("modalEditAlbum");

    const openModalBtn = document.getElementById("openModalBtn");
    const closeModalBtns = document.querySelectorAll(".close-modal");

    const confirmDeleteBtn = document.getElementById("confirmDeleteAlbum");
    const deleteAlbumId = document.getElementById("deleteAlbumId");

    const modalTitle = document.getElementById("modalTitle");
    const modalPhotos = document.getElementById("modalPhotos");
    const uploadAlbumId = document.getElementById("uploadAlbumId");

    const modalShareAlbum = document.getElementById("modalShareAlbum");
    const shareLinkInput = document.getElementById("shareLink");
    const copyShareLinkBtn = document.getElementById("copyShareLink");
    const closeShareModal = document.getElementById("closeShareModal");

    if (openModalBtn) {
        openModalBtn.addEventListener("click", function () {
            modalAlbum.style.display = "flex";
        });
    }

    closeModalBtns.forEach(button => {
        button.addEventListener("click", function () {
            modalAlbum.style.display = "none";
            albumModal.style.display = "none";
            modalDelete.style.display = "none";
            modalEdit.style.display = "none";
        });
    });

    window.addEventListener("click", function (event) {
        if (event.target === modalAlbum) modalAlbum.style.display = "none";
        if (event.target === albumModal) albumModal.style.display = "none";
        if (event.target === modalDelete) modalDelete.style.display = "none";
        if (event.target === modalEdit) modalEdit.style.display = "none";
    });

    document.addEventListener("click", function (event) {
        const optionsBtn = event.target.closest(".options-btn");
        if (optionsBtn) {
            event.stopPropagation(); 
            const menu = optionsBtn.nextElementSibling;
            document.querySelectorAll(".options-menu").forEach(m => {
                if (m !== menu) m.classList.remove("active");
            });
            menu.classList.toggle("active");
            return;
        }

        if (!event.target.closest(".options-menu")) {
            document.querySelectorAll(".options-menu").forEach(menu => {
                menu.classList.remove("active");
            });
        }
    });

    document.addEventListener("click", function (event) {
        if (!event.target.classList.contains("delete-album")) return;
        event.stopPropagation();
        const albumId = event.target.dataset.groupId;
        deleteAlbumId.value = albumId;
        modalDelete.style.display = "flex";
    });

    confirmDeleteBtn.addEventListener("click", function () {
        const albumId = deleteAlbumId.value;

        fetch(`/album/delete`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ album_id: albumId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Album supprimé avec succès !");
                location.reload();
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => console.error("Erreur :", error));

        modalDelete.style.display = "none";
    });

    document.querySelectorAll(".edit-album").forEach(button => {
        button.addEventListener("click", function () {
            const albumId = this.dataset.groupId;
            document.getElementById("editAlbumId").value = albumId;
            document.getElementById("modalEditAlbum").style.display = "flex";
        });
    });
    
    document.getElementById("confirmEditAlbum").addEventListener("click", function () {
        const albumId = document.getElementById("editAlbumId").value;
        const newName = document.getElementById("newAlbumName").value.trim();
    
        if (!newName) {
            alert("Le nom ne peut pas être vide.");
            return;
        }
    
        console.log("Données envoyées :", { album_id: albumId, new_name: newName });
    
        fetch(`/album/update`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ album_id: albumId, new_name: newName })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Réponse reçue :", data);
    
            if (data.success) {
                alert("Album modifié avec succès !");
                location.reload();
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => console.error("Erreur :", error));
    
        document.getElementById("modalEditAlbum").style.display = "none";
    });
    

    document.addEventListener("click", function (event) {
        if (event.target.closest(".options-btn") || event.target.closest(".options-menu")) return;

        const card = event.target.closest(".album-card");
        if (!card) return;

        const groupId = card.getAttribute("data-group-id");
        const groupName = card.getAttribute("data-group-name");
        const photos = JSON.parse(card.getAttribute("data-photos") || "[]");

        modalTitle.textContent = groupName;
        modalPhotos.innerHTML = "";
        uploadAlbumId.value = groupId;

        if (photos.length > 0) {
            photos.forEach(photo => {
                const photoCard = document.createElement("div");
                photoCard.classList.add("photo-card");

                const img = document.createElement("img");
                img.src = `/uploads/${photo}`;
                img.alt = "Photo de l'album";
                img.style.width = "100%";

                photoCard.appendChild(img);
                modalPhotos.appendChild(photoCard);
            });
        } else {
            modalPhotos.innerHTML = "<p>Aucune photo dans cet album.</p>";
        }

        albumModal.style.display = "flex";
    });

    document.getElementById("uploadPhotoForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
    
        fetch("/photo/upload", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.photos.forEach(photo => {
                    const photoCard = document.createElement("div");
                    photoCard.classList.add("photo-card");
    
                    const img = document.createElement("img");
                    img.src = `/uploads/${photo}`;
                    img.alt = "Nouvelle photo";
                    img.style.width = "100%";
    
                    photoCard.appendChild(img);
                    document.getElementById("modalPhotos").appendChild(photoCard);
                });
    
                alert("Photos ajoutées avec succès !");
                this.reset();
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error("Erreur lors de l'upload :", error));
    });

    document.addEventListener("click", function (event) {
        if (!event.target.classList.contains("share-album")) return;
    
        const albumId = event.target.dataset.groupId;
    
        fetch(`/album/get-permission?album_id=${albumId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP : ${response.status}`);
            }
            return response.json();
        })
        .then(data => {    
            if (data.success) {
                const shareLinkInput = document.getElementById("shareLink");
                shareLinkInput.value = data.share_url;
                document.getElementById("modalShareAlbum").style.display = "flex";
    
                const toggleUploadPermission = document.getElementById("toggleUploadPermission");
                toggleUploadPermission.checked = (data.permission === "can_upload");
                toggleUploadPermission.dataset.albumId = albumId;
    
                document.getElementById("permissionInfo").innerText = data.permission === "can_upload"
                    ? "Les utilisateurs peuvent ajouter des photos."
                    : "Les utilisateurs ne peuvent que voir l'album.";
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => console.error("Erreur :", error));
    });
    
    
    
    
    document.getElementById("toggleUploadPermission").addEventListener("change", function () {
        const albumId = this.dataset.albumId;
        if (!albumId) {
            alert("Erreur : Aucun album sélectionné.");
            return;
        }
    
        const newPermission = this.checked ? "can_upload" : "read_only";
    
        fetch("/album/update-permission", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ album_id: albumId, permission: newPermission })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("permissionInfo").innerText = newPermission === "can_upload"
                    ? "Les utilisateurs peuvent ajouter des photos."
                    : "Les utilisateurs ne peuvent que voir l'album.";
            } else {
                alert("Erreur : " + data.error);
                this.checked = !this.checked; 
            }
        })
        .catch(error => {
            console.error("Erreur :", error);
            this.checked = !this.checked;
        });
    });
    
    
    document.getElementById("copyShareLink").addEventListener("click", function () {
        const shareLinkInput = document.getElementById("shareLink");
        shareLinkInput.select();
        document.execCommand("copy");
        alert("Lien copié dans le presse-papier !");
    });
    
    document.getElementById("closeShareModal").addEventListener("click", function () {
        document.getElementById("modalShareAlbum").style.display = "none";
    });

});
