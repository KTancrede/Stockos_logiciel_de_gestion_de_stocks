<?php
include '../includes/verifier_connexion.php';
verifier_connexion();
/* biere bouteille- liqueur - alcool -sirop*/
function getUserRole() {
    // Remplacez ceci par votre logique pour obtenir le rôle de l'utilisateur connecté
    // Par exemple, vous pouvez récupérer le rôle à partir de la session ou de la base de données
    return $_SESSION['user_role']; // Exemple : 'patron', 'manager', 'employe'
}

$userRole = getUserRole();
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
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/icons/apple-touch-icon.png">
    <link rel="icon" sizes="192x192" href="../assets/icons/android-chrome-192x192.png">
    <link rel="icon" sizes="512x512" href="../assets/icons/android-chrome-512x512.png">

</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
        <?php if ($userRole === 'patron'): ?>
        <a href="reglages.php" class="settings-link">
            <img class='img-reg' src='../assets/images/logo_reglage.webp' alt='Paramètres'/>
        </a>
        <?php endif; ?>
        <a href="deconnexion.php" class="logout-link">
            <img class='img-logout' src='../assets/images/logout_icon.png' alt='Déconnexion'/>
        </a>
    </header>
    <main>
    <div class="home-container">
        <div class="button-group">
            <a href="inventaire.php" class="button">Creer bon de commande</a>
            <?php if ($userRole === 'patron'): ?>
            <a href="affichage_stock.php" class="button">Affichage des stocks</a>
            <?php endif; ?>
            <?php if ($userRole === 'patron' || $userRole === 'responsable'): ?>
            <a href="ajout_suppression.php" class="button">Ajout/Suppression de produit</a>
            <?php endif; ?>
            
        </div>
        <div class="button-group">
            <?php if ($userRole === 'patron' || $userRole === 'responsable'): ?>
            <a href="ajout_suppression_fournisseur.php" class="button">Ajout/Suppression de fournisseur</a>
            <?php endif; ?>
            <a href="liens_prod_four.php" class="button">Affichage liens produits-fournisseurs</a>
            <?php if ($userRole === 'patron'): ?>
            <a href="ajout_employe.php" class="button">Ajout employé</a>
            <?php endif; ?>
        </div>
    </div>
    </main>
</body>
</html>
