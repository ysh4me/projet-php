<div class="container">
    <nav class="navbar">
        <div class="navbar-brand">BuddyShotz</div>
        <ul class="nav-links">
            <li><a href="/">Accueil</a></li>
            <?php if (!empty($_SESSION['user']) && isset($_SESSION['user']['firstname'])): ?>
                <li><a href="#">Feed</a></li>
                <li><a href="#">Upload</a></li>
                <li><a href="#">Publish</a></li>
                <li><a href="/logout">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="#">Feed</a></li>
                <li><a href="#">Upload</a></li>
                <li><a href="#">Publish</a></li>
                <li><a href="/login">Connexion</a></li>
                <li><a href="/register">Inscription</a></li>
            <?php endif; ?>

        </ul>
    </nav>
</div>

