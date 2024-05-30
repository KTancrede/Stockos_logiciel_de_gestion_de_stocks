<?php
include 'connexion.php';

function supprimer_produit($id) {
    $conn = connectDB();
    $sql = "DELETE FROM produits WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Produit supprimé avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    supprimer_produit($id);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='shortcut icon' type='image/x-icon' href='images/favicon/favicon.ico'/>
    <link rel="stylesheet" href="style.css">
    <title>Supprimer Produit</title>
</head>
<body>
    <header>
        <div class="logo">
            Stockos
        </div>
    </header>
    <main>
        <div class="container">
            <h2>Supprimer un Produit</h2>
            <form method="POST" action="">
                <label for="id">ID du Produit</label>
                <input type="number" name="id" id="id" required><br>
                <input type="submit" value="Supprimer">
            </form>
        </div>
    </main>
</body>
</html>
