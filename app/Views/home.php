<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php include __DIR__ . '/partials/head.php'; ?>

<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <section class="hero">
        <div class="container">
        <div class="hero-image">
        <div class="image-container">
            <div class="hero-text">
                <h2>Discover the world's Hidden Wonders</h2>
                <p>Wonders</p>
                <button class="btn">JOIN US</button>
            </div>
                </div>
            </div>
        </div>
    </section>

    <section class="share">
        <div class="container">
            <h2>Share Your Moment With Us</h2>
            <div class="image-grid">
                <div class="image-container">
                    <div class="image" style="background-image: url('../images/1.jpg');"></div>
                    <br>
                    <p>India</p>
                    <div class="actions">
                            <button class="like">👍</button>
                            <button class="dislike">👎</button>
                            <span class="rating">⭐ 3.8</span>
                        </div>
                </div>
                <div class="image-container">
                    <div class="image" style="background-image: url('../images/2.jpg');"></div>
                    <br>
                    <p>Australie</p>
                    <div class="actions">
                            <button class="like">👍</button>
                            <button class="dislike">👎</button>
                            <span class="rating">⭐ 3.8</span>
                        </div>
                </div>
                <div class="image-container">
                    <div class="image" style="background-image: url('../images/3.jpg');"></div>
                    <br>
                    <p>Turkey</p>
                    <div class="actions">
                            <button class="like">👍</button>
                            <button class="dislike">👎</button>
                            <span class="rating">⭐ 3.8</span>
                        </div>
                </div>
                <div class="image-container">
                    <div class="image" style="background-image: url('../images/4.jpg');"></div>
                    <br>
                    <p>North America</p>
                    <div class="actions">
                            <button class="like">👍</button>
                            <button class="dislike">👎</button>
                            <span class="rating">⭐ 3.8</span>
                        </div>
                </div>
            </div>
        </div>
    </section>


    <section class="latest-stories">
        <h2>Latest Stories</h2>
        <div class="container">
            <div class="small-image-grid left">
                <div class="small-image-container">
                    <img src="../images/6.jpg" alt="Small Image 1">
                    <p>Description 1</p>
                </div>
                <div class="small-image-container">
                    <img src="../images/5.jpg" alt="Small Image 2">
                    <p>Description 2</p>
                </div>
                <div class="small-image-container">
                    <img src="../images/5.jpg" alt="Small Image 3">
                    <p>Description 3</p>
                </div>
            </div>
            <div class="featured-image">
                <img src="../images/7.jpg" alt="Main Image">
            </div>
            <div class="small-image-grid right">
                <div class="small-image-container">
                    <img src="../images/6.jpg" alt="Small Image 4">
                    <p>Description 4</p>
                </div>
                <div class="small-image-container">
                    <img src="../images/5.jpg" alt="Small Image 5">
                    <p>Description 5</p>
                </div>
                <div class="small-image-container">
                    <img src="../images/5.jpg" alt="Small Image 6">
                    <p>Description 6</p>
                </div>
            </div>
        </div>
    </section>


    <section class="subscribe">
        <div class="container">
            <h2>Get Your Travel Inspiration Straight To Your Inbox</h2>
            <input type="text" placeholder="Enter your email">
            <button class="btn">Get Started</button>
        </div>
    </section>


<?php include __DIR__ . '/partials/footer.php'; ?>