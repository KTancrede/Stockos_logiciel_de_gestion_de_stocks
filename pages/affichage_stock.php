<?php
include '../includes/connexion.php';
include '../includes/verifier_connexion.php';
verifier_connexion();

// Fonction pour récupérer tous les produits avec leurs fournisseurs
function getAllProducts() {
    $conn = connectDB();
    $sql = "
        SELECT p.nom, p.type, p.marque, p.quantite, p.quantite_max, p.enStock, p.fournisseur, f.email, f.numero_telephone
        FROM produits p
        LEFT JOIN fournisseur f ON p.fournisseur = f.nom
    ";
    $stmt = $conn->query($sql);
    $products = [];

    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $row;
        }
    }

    return $products;
}

function getProgressBarClass($pourcentage) {
    if ($pourcentage <= 20) {
        return 'progress-bar-red';
    } elseif ($pourcentage <= 50) {
        return 'progress-bar-orange';
    } else {
        return 'progress-bar-green';
    }
}

$products = getAllProducts();

function filterProductsByType($products, $type) {
    return array_filter($products, function($product) use ($type) {
        return $product['type'] === $type;
    });
}

$futProducts = filterProductsByType($products, 'fût');
$softProducts = filterProductsByType($products, 'soft');
$hardProducts = filterProductsByType($products, 'hard');
$vinProducts = filterProductsByType($products, 'vin');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Affichage Stock</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>
    <main>
        <div class="stock-container">
            <h2>Affichage des Stocks</h2>

            <?php
            function displayTable($products, $type) {
                echo "<h3 class='sous-titre'>". strtoupper($type)."</h3>";
                echo '<table class="stock-table">';
                echo '<tr>';
                echo '<th>Nom</th>';
                echo '<th>Marque</th>';
                echo '<th>Fournisseur</th>';
                echo '<th>Quantité (L)</th>';
                echo '<th>Quantité Max</th>';
                echo '<th>Actuellement en stock</th>';
                echo '<th>Pourcentage</th>';
                echo '</tr>';

                if (count($products) > 0) {
                    foreach ($products as $product) {
                        $pourcentage = ($product['enStock'] / $product['quantite_max']) * 100;
                        $progressBarClass = getProgressBarClass($pourcentage);
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($product['nom']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['marque']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['fournisseur']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['quantite']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['quantite_max']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['enStock']) . '</td>';
                        echo '<td><div class="progress-bar"><div class="' . $progressBarClass . '" style="width:' . $pourcentage . '%;">' . round($pourcentage, 2) . '%</div></div></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8">Aucun produit trouvé.</td></tr>';
                }

                echo '</table>';
            }

            displayTable($futProducts, 'fût');
            displayTable($softProducts, 'soft');
            displayTable($hardProducts, 'hard');
            displayTable($vinProducts, 'vin');
            ?>
        </div>
    </main>
</body>
</html>
