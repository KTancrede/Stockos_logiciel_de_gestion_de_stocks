<?php
include 'connexion.php';
$message="";

function ajouter_produit($nom, $type, $marque, $quantite, $fournisseur, $prix, $quantite_max) {
    $conn = connectDB();
    if ($conn) {
        try {
            $sql = "INSERT INTO produits (nom, type, marque, quantite, fournisseur, prix, quantite_max) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            // Utilisation de bindParam avec des positions
            $stmt->bindParam(1, $nom);
            $stmt->bindParam(2, $type);
            $stmt->bindParam(3, $marque);
            $stmt->bindParam(4, $quantite);
            $stmt->bindParam(5, $fournisseur);
            $stmt->bindParam(6, $prix);
            $stmt->bindParam(7, $quantite_max);

            if ($stmt->execute()) {
                $message="Produit ajouté avec succès";
            } else {
                echo "Erreur: " . implode(", ", $stmt->errorInfo());
            }
        } catch (PDOException $e) {
            $message = "Erreur: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $type = htmlspecialchars($_POST['type']);
    $marque = htmlspecialchars($_POST['marque']);
    $quantite = htmlspecialchars($_POST['quantite']);
    $fournisseur = htmlspecialchars($_POST['fournisseur']);
    $prix = htmlspecialchars($_POST['prix']);
    $quantite_max=htmlspecialchars($_POST['quantite_max']);
    ajouter_produit($nom, $type, $marque, $quantite, $fournisseur, $prix, $quantite_max);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='shortcut icon' type='image/x-icon' href='images/favicon/favicon.ico'/>
    <link rel="stylesheet" href="style.css">
    <title>Ajouter Produit</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>

    <main>
        <div class="form_ajouter_container">
            <h2>Ajouter un Produit</h2>
            <form method="POST" action="">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required><br>
                <label for="type">Type</label>
                <select name="type" id="type" required>
                    <option value="" disabled selected>Choisir un type</option>
                    <option value="fû">Fût</option>
                    <option value="soft">Soft</option>
                    <option value="hard">Hard</option>
                    <option value="vin">Vin</option>
                </select><br>
                <label for="marque">Marque/Maison</label>
                <input type="text" name="marque" id="marque" required><br>
                <label for="quantite">Quantité (L)</label>
                <input type="number" step="0.01" name="quantite" id="quantite" required><br>
                <label for="fournisseur">Fournisseur</label>
                <input type="text" name="fournisseur" id="fournisseur" required><br>
                <label for="prix">Prix (€)</label>
                <input type="number" step="0.01" name="prix" id="prix" required><br>
                <label for="quantite_max">Quantité max stockable</label>
                <input type="number" name="quantite_max" id="quantite_max" required><br>
                <input type="submit" value="Ajouter">
            </form>
            <?php
            if ($message) {
                echo "<div id='msg_ajout'>$message</div>";
            }
            ?>
        </div>
    </main>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            var msg = document.getElementById('msg_ajout');
            if (msg) {
                msg.style.display = 'none';
            }
        }, 5000); 
    });
    </script>

</body>
</html>
