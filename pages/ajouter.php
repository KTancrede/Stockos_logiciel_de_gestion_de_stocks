<?php
include '../includes/connexion.php';
include '../includes/verifier_connexion.php';
verifier_connexion();

$message = "";

function ajouter_produit($nom, $type, $marque, $quantite, $fournisseur, $prix, $quantite_max, $image = null) {
    global $message;
    $conn = connectDB();
    if ($conn) {
        try {
            $sql = "INSERT INTO produits (nom, type, marque, quantite, fournisseur, prix, quantite_max, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(1, $nom);
            $stmt->bindParam(2, $type);
            $stmt->bindParam(3, $marque);
            $stmt->bindParam(4, $quantite);
            $stmt->bindParam(5, $fournisseur);
            $stmt->bindParam(6, $prix);
            $stmt->bindParam(7, $quantite_max);
            $stmt->bindParam(8, $image);

            if ($stmt->execute()) {
                $message .= "Produit ajouté avec succès";
            } else {
                $message .= "Erreur: " . implode(", ", $stmt->errorInfo());
            }
        } catch (PDOException $e) {
            $message .= "Erreur: " . $e->getMessage();
        }
    } else {
        $message .= "Erreur de connexion à la base de données.";
    }
}

function getAllFournisseurs() {
    $conn = connectDB();
    $sql = "SELECT nom FROM fournisseur";
    $stmt = $conn->query($sql);
    $fournisseurs = [];

    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fournisseurs[] = $row['nom'];
        }
    }

    return $fournisseurs;
}

$fournisseurs = getAllFournisseurs();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $type = htmlspecialchars($_POST['type']);
    $marque = htmlspecialchars($_POST['marque']);
    $quantite = htmlspecialchars($_POST['quantite']);
    $fournisseur = htmlspecialchars($_POST['fournisseur']);
    $prix = htmlspecialchars($_POST['prix']);
    $quantite_max = htmlspecialchars($_POST['quantite_max']);
    $image = null;

    // Gestion du téléchargement de l'image
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifiez si l'image est une image réelle ou une fausse image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $message .= "Le fichier n'est pas une image. ";
            $uploadOk = 0;
        }

        // Vérifiez la taille du fichier
        if ($_FILES["image"]["size"] > 2000000) {
            $message .= "Désolé, votre fichier est trop volumineux. ";
            $uploadOk = 0;
        }

        // Limiter les formats de fichiers
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $message .= "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés. ";
            $uploadOk = 0;
        }

        // Vérifiez si $uploadOk est mis à 0 par une erreur
        if ($uploadOk == 0) {
            $message .= "Désolé, votre fichier n'a pas été téléchargé. ";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $target_file;
            } else {
                $message .= "Désolé, une erreur s'est produite lors du téléchargement de votre fichier. ";
            }
        }
    }

    // Ajouter le produit avec ou sans image
    ajouter_produit($nom, $type, $marque, $quantite, $fournisseur, $prix, $quantite_max, $image);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
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
            <form method="POST" action="ajouter.php" enctype="multipart/form-data">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required><br>
                <label for="type">Type</label>
                <select name="type" id="type" required>
                    <option value="" disabled selected>Choisir un type</option>
                    <option value="fût">Fût</option>
                    <option value="soft">Soft</option>
                    <option value="hard">Hard</option>
                    <option value="vin">Vin</option>
                </select><br>
                <label for="marque">Marque/Maison</label>
                <input type="text" name="marque" id="marque" required><br>
                <label for="quantite">Quantité (L)</label>
                <input type="number" step="0.01" name="quantite" id="quantite" required><br>
                <label for="fournisseur">Fournisseur</label>
                <select name="fournisseur" id="fournisseur" required>
                    <option value="" disabled selected>Choisir un fournisseur</option>
                    <?php foreach ($fournisseurs as $fournisseur) : ?>
                        <option value="<?php echo htmlspecialchars($fournisseur); ?>"><?php echo htmlspecialchars($fournisseur); ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="prix">Prix (€)</label>
                <input type="number" step="0.01" name="prix" id="prix" required><br>
                <label for="quantite_max">Quantité max stockable</label>
                <input type="number" name="quantite_max" id="quantite_max" required><br>
                <label for="image">Image (optionnel)</label>
                <input type="file" name="image" id="image" accept="image/*"><br>
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
