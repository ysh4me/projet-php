<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Buddy Shotz - Validation de votre email</title>
</head>
<body>
    <h3>Bienvenue sur Buddy Shotz !</h3>
    <p>Merci de vous être inscrit. Pour valider votre compte, cliquez sur le bouton ci-dessous :</p>
    <a href="<?= htmlspecialchars($verificationLink, ENT_QUOTES, 'UTF-8') ?>" style="display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;">
        Valider mon email
    </a>
    <p>Ou copiez ce lien dans votre navigateur :</p>
    <p><strong><?= htmlspecialchars($verificationLink, ENT_QUOTES, 'UTF-8') ?></strong></p>
</body>
</html>
