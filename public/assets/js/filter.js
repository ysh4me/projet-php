document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const albums = document.querySelectorAll(".album-card");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            const filter = this.getAttribute("data-filter");

            filterButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");

            albums.forEach(album => {
                const isShared = album.dataset.isShared === "1";

                if (filter === "all") {
                    album.style.display = "block";
                } else if (filter === "shared-by-me" && isShared) {
                    album.style.display = "block";
                } else {
                    album.style.display = "none";
                }
            });
        });
    });
});
