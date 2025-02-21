<div class="container">
    <nav class="navbar">
        <div class="navbar-left">
            <a href="/" class="navbar-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M11.5 8C14 8 16 10 16 12.5S14 17 11.5 17S7 15 7 12.5S9 8 11.5 8m0 1A3.5 3.5 0 0 0 8 12.5a3.5 3.5 0 0 0 3.5 3.5a3.5 3.5 0 0 0 3.5-3.5A3.5 3.5 0 0 0 11.5 9M5 5h2l2-2h5l2 2h2a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V8a3 3 0 0 1 3-3m4.41-1l-2 2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-2.41l-2-2z" />
                </svg>
                <span>Buddy Shotz</span>
            </a>
        </div>

        <ul class="nav-links">
            <li><a href="/">Accueil</a></li>
            <?php if (!empty($_SESSION['user']) && isset($_SESSION['user']['firstname'])): ?>
                <li><a href="/albums">Albums</a></li>
                <li><a href="/logout">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="/login">Connexion</a></li>
                <li><a href="/register">Inscription</a></li>
            <?php endif; ?>
        </ul>

        <button id="burger-btn" class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div id="mobile-menu" class="mobile-menu">
            <button id="close-menu" class="close-btn"></button>
            <ul>
                <li><a href="/">Accueil</a></li>
                <?php if (!empty($_SESSION['user']) && isset($_SESSION['user']['firstname'])): ?>
                    <li><a href="/albums">Albums</a></li>
                    <li><a href="/logout">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="/login">Connexion</a></li>
                    <li><a href="/register">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const burgerBtn = document.getElementById("burger-btn");
        const mobileMenu = document.getElementById("mobile-menu");
        const closeMenu = document.getElementById("close-menu");

        if (!burgerBtn || !mobileMenu || !closeMenu) {
            console.error("Erreur : Éléments non trouvés dans le DOM.");
            return;
        }

        burgerBtn.addEventListener("click", function () {
            mobileMenu.classList.toggle("active");
        });

        closeMenu.addEventListener("click", function () {
            mobileMenu.classList.remove("active");
        });

        document.addEventListener("click", function (event) {
            if (!mobileMenu.contains(event.target) && !burgerBtn.contains(event.target)) {
                mobileMenu.classList.remove("active");
            }
        });
    });

</script>