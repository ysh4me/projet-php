<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/navbar.php'; ?>


<h1>Hello home !</h1>
<h1>Bienvenu chez Buddy Shotz</h1>

<?php include __DIR__ . '/partials/footer.php'; ?>