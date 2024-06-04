<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Ajouter Produit</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>

        <div class="form_container">
            <h2>Créer un compte bar</h2>
            <form method="POST" action="creer_compte.php">
                <label for="bar_name">Nom du bar</label>
                <input type="text" name="bar_name" id="bar_name" required><br>
                <label for="login">Identifiant</label>
                <input type="text" name="login" id="login" required><br>
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required><br>
                <input type="submit" value="Créer le compte">
            </form>
        </div>
    </main>
</body>
</html>

<?php
include '../includes/connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barName = htmlspecialchars($_POST['bar_name']);
    $login = htmlspecialchars($_POST['login']);
    $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);

    createBarAccount($barName, $login, $mot_de_passe);
}
?>

