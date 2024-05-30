<?php
include 'connexion.php';

// Fonction pour récupérer tous les produits
function getAllProducts() {
    $conn = connectDB();
    $sql = "SELECT * FROM produits";
    $stmt = $conn->query($sql);
    $products = [];
    
    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $row;
        }
    }
    
    return $products;
}


// Fonction pour supprimer les produits sélectionnés
function supprimer_produits($ids) {
    $conn = connectDB();
    $sql = "DELETE FROM produits WHERE id IN (" . implode(',', array_map('intval', $ids)) . ")";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        echo "Produits supprimés avec succès.";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ids'])) {
    $ids = $_POST['ids'];
    supprimer_produits($ids);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel='shortcut icon' type='image/x-icon' href='images/favicon/favicon.ico'/>
    <title>Supprimer Produit</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>

    <main>
        <div class="form_supprimer_container">
            <h2>Supprimer des Produits</h2>
            <form method="POST" action="">
                <?php
                $products = getAllProducts();
                if (count($products) > 0) {
                    foreach ($products as $product) {
                        echo '<div class="product-item">';
                        echo '<input type="checkbox" name="ids[]" value="' . $product['id'] . '">';
                        echo '<label>' . $product['nom'] . ' (' . $product['type'] . ')</label>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Aucun produit trouvé.</p>';
                }
                ?>
                <input type="submit" value="Supprimer">
            </form>
        </div>
    </main>
</body>
</html>
