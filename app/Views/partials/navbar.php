<nav>
    <ul>
        <li><a href="/">Accueil</a></li>
        <?php if (!empty($_SESSION['user']) && isset($_SESSION['user']['firstname'])): ?>
            <li>Bienvenue, <?= htmlspecialchars($_SESSION['user']['firstname']) ?> !</li>
            <li><a href="/logout">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="/login">Connexion</a></li>
            <li><a href="/register">Inscription</a></li>
        <?php endif; ?>

    </ul>
</nav>
