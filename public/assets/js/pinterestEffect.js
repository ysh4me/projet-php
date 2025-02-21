document.addEventListener("DOMContentLoaded", function () {
    const modalPhotos = document.getElementById("modalPhotos");

    function applyMasonryEffect() {
        modalPhotos.style.display = "grid";
        modalPhotos.style.gridTemplateColumns = "repeat(auto-fill, minmax(250px, 1fr))";
        modalPhotos.style.gap = "2rem";
        modalPhotos.style.alignItems = "start"; 
    }

    function adjustModalHeight() {
        const modalContent = document.querySelector("#albumModal .modal-content");
        modalContent.style.maxHeight = "90vh";
        modalContent.style.overflowY = "auto";
    }

    applyMasonryEffect();
    adjustModalHeight();
});
