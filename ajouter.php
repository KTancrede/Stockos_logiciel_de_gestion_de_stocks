<?php
include 'connexion.php';

function ajouter_produit($nom, $type, $marque, $quantite, $fournisseur, $prix) {
    $conn = connectDB();
    $sql = "INSERT INTO produits (nom, type, marque, quantite, fournisseur, prix) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt->bind_param("sssdsd", $nom, $type, $marque, $quantite, $fournisseur, $prix);

    if ($stmt->execute()) {
        echo "Produit ajouté avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $type = $_POST['type'];
    $marque = $_POST['marque'];
    $quantite = $_POST['quantite'];
    $fournisseur = $_POST['fournisseur'];
    $prix = $_POST['prix'];
    ajouter_produit($nom, $type, $marque, $quantite, $fournisseur, $prix);
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
        <div class="logo">
            Stockos
        </div>
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
                    <option value="fû">Fû</option>
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
                <input type="submit" value="Ajouter">
            </form>
        </div>
    </main>
</body>
</html>
