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
    <title>Ajout Suppression</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>

    <main>  
        <div>
            <h1 class='titre-ajout'>
                Ajout/Suppression des produits
            </h1>
        </div>
        <div class="button-container">
            <button class="action-button add-button" onclick="window.location.href='ajouter.php'">+</button>
            <button class="action-button delete-button" onclick="window.location.href='supprimer.php'">-</button>
        </div>
    </main>
</body>
</html>
