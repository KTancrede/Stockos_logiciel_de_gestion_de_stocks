<?php
include '../includes/verifier_connexion.php';
verifier_connexion();

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML Basic 1.1//EN'
 'http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Page d'accueil - Stockos</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
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
            <a href="liens_prod_four.php" class="button">Affichage liens produits-fournisseurs</a>
            <a href="page6.php" class="button">Autre</a>
        </div>
    </div>
    </main>
</body>
</html>