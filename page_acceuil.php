<?php
// Vérifie si l'utilisateur est connecté

/*if (!isset($_SESSION['user_id'])) {
    // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: index.php");
    exit(); // Assurez-vous de terminer le script après la redirection
}*/

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML Basic 1.1//EN'
 'http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='shortcut icon' type='image/x-icon' href='images/favicon/favicon.ico'/> 
    <title>Page d'accueil - Stockos</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <header>
        <div class="logo">
            Stockos
        </div>
    </header>
    <main>
    <div class="home-container">
        <div class="button-group">
            <a href="inventaire.php" class="button">Faire l'inventaire</a>
            <a href="affichage_stock.php" class="button">Affichage des stocks</a>
            <a href="ajout_suppression.php" class="button">Ajout/Suppression de produit</a>
        </div>
        <div class="button-group">
            <a href="ajout_suppression_fournisseur.php" class="button">Ajout/Suppression de fournisseur</a>
            <a href="page5.php" class="button">Affichage liens produits-fournisseurs</a>
            <a href="page6.php" class="button">Autre</a>
        </div>
    </div>
    </main>
</body>
</html>