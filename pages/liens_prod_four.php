<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/verifier_connexion.php';
include '../includes/connexion.php';
verifier_connexion();

// Fonction pour récupérer les fournisseurs et leurs produits
function getFournisseursEtProduits() {
    $conn = connectDB();
    $sql = "
        SELECT f.nom AS fournisseur_nom, p.nom AS produit_nom, p.type, p.marque, p.quantite, p.prix 
        FROM fournisseur f
        LEFT JOIN produits p ON f.nom = p.fournisseur
        ORDER BY f.nom, p.nom";
    $stmt = $conn->query($sql);
    $fournisseursProduits = [];

    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fournisseursProduits[$row['fournisseur_nom']][] = $row;
        }
    }

    return $fournisseursProduits;
}

$fournisseursProduits = getFournisseursEtProduits();
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML Basic 1.1//EN'
 'http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/>
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8'/>
    <title>Liens Produits-Fournisseurs - Stockos</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>
    <main>
        <div class="containere">
            <h1 class="page-title">Liens Produits-Fournisseurs</h1>
            <?php foreach ($fournisseursProduits as $fournisseur => $produits) : ?>
                <div class="fournisseur-section">
                    <h2 class="fournisseur-title"><?php echo htmlspecialchars($fournisseur); ?></h2>
                    <table class="produits-table">
                        <thead>
                            <tr>
                                <th>Nom du produit</th>
                                <th>Type</th>
                                <th>Marque</th>
                                <th>Quantité (L)</th>
                                <th>Prix (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produits as $produit) : ?>
                                <tr>
                                    <td><?php echo ($produit['produit_nom']); ?></td>
                                    <td><?php echo ($produit['type']); ?></td>
                                    <td><?php echo ($produit['marque']); ?></td>
                                    <td><?php echo ($produit['quantite']); ?></td>
                                    <td><?php echo ($produit['prix']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>