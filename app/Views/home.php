<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <section class="how-it-works">
        <div class="container">
            <h2>Comment ça marche ?</h2>
            <p>Découvrez comment Buddy Shotz facilite le partage et la gestion de vos souvenirs.</p>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Importez vos photos</h3>
                    <p>Ajoutez facilement vos clichés de voyage pour les conserver en toute sécurité.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Invitez vos amis</h3>
                    <p>Créez des albums et partagez vos souvenirs avec vos proches.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Revivez vos souvenirs</h3>
                    <p>Accédez à votre galerie à tout moment, depuis n'importe quel appareil.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="hero">
        <div class="container">
            <h1>Capturez et Partagez Vos Moments de Roadtrip</h1>
            <p>Rejoignez la communauté Buddy Shotz et immortalisez vos voyages.</p>
            <a href="/register" class="btn">Rejoignez-nous</a>
        </div>
    </section>

    <section class="intro">
        <div class="container">
            <h2>🌍 Pourquoi choisir <span>Buddy Shotz</span> ?</h2>
            <p>Un espace unique pour stocker, organiser et partager vos souvenirs de voyage en toute simplicité.</p>
            <div class="benefits">
                <div class="benefit">
                    <h3>📌 Une plateforme pensée pour vous</h3>
                    <p>Créez des albums et partagez vos photos de voyage avec vos amis et votre famille.</p>
                </div>
                <div class="benefit">
                    <h3>🔒 Sécurisez vos souvenirs</h3>
                    <p>Vos photos restent privées, visibles uniquement par les personnes que vous choisissez.</p>
                </div>
                <div class="benefit">
                    <h3>🚀 Accédez à vos souvenirs n’importe où</h3>
                    <p>Disponible sur mobile, tablette et ordinateur. Votre galerie vous suit partout.</p>
                </div>
            </div>
            <a href="/register" class="btn">Commencer maintenant</a>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="feature">
                <h2>📸 Partagez vos photos</h2>
                <p>Uploadez vos clichés de roadtrip et partagez-les avec vos amis.</p>
            </div>
            <div class="feature">
                <h2>🚀 Créez des groupes</h2>
                <p>Invitez vos amis et créez un espace dédié à vos souvenirs.</p>
            </div>
            <div class="feature">
                <h2>🔒 Protégez vos souvenirs</h2>
                <p>Définissez qui peut voir vos photos grâce à des options de confidentialité avancées.</p>
            </div>
        </div>
    </section>

    <section class="latest-uploads">
        <div class="container">
            <h2>🌍 Dernières Photos Partagées</h2>
            <div class="photo-grid">
                <div class="photo" style="background-image: url('../images/1.jpg');"></div>
                <div class="photo" style="background-image: url('../images/2.jpg');"></div>
                <div class="photo" style="background-image: url('../images/3.jpg');"></div>
                <div class="photo" style="background-image: url('../images/4.jpg');"></div>
            </div>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <h2>💬 Ce que nos utilisateurs disent</h2>
            <div class="testimonial-list">
                <div class="testimonial">
                    <p>“Buddy Shotz est la meilleure plateforme pour garder mes souvenirs de voyage. J'adore partager mes photos avec mes amis !”</p>
                    <span>- Sophie, Voyageuse passionnée</span>
                </div>
                <div class="testimonial">
                    <p>“Facile à utiliser et très sécurisé, je recommande Buddy Shotz à tous les amateurs de roadtrips.”</p>
                    <span>- Marc, Aventurier en van</span>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
