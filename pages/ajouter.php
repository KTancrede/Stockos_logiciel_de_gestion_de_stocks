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

function compressAndResizeImage($source, $destination, $quality, $new_width, $new_height) {
    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } else {
        return false;
    }

    // Lire les données Exif si l'image est un JPEG
    if ($info['mime'] == 'image/jpeg') {
        $exif = exif_read_data($source);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }
        }
    }

    // Dimensions de l'image source
    $width = imagesx($image);
    $height = imagesy($image);

    // Calculer les nouvelles dimensions tout en maintenant le ratio
    $ratio_orig = $width / $height;
    if ($new_width / $new_height > $ratio_orig) {
        $new_width = $new_height * $ratio_orig;
    } else {
        $new_height = $new_width / $ratio_orig;
    }

    // Créer une nouvelle image vide avec les nouvelles dimensions
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Maintenir la transparence pour les PNG et GIF
    if ($info['mime'] == 'image/png') {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
    } elseif ($info['mime'] == 'image/gif') {
        $transparent_index = imagecolortransparent($image);
        if ($transparent_index >= 0) {
            $transparent_color = imagecolorsforindex($image, $transparent_index);
            $transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
            imagefill($new_image, 0, 0, $transparent_index);
            imagecolortransparent($new_image, $transparent_index);
        }
    }

    // Redimensionner l'image
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Enregistrer l'image compressée et redimensionnée
    if ($info['mime'] == 'image/jpeg') {
        imagejpeg($new_image, $destination, $quality);
    } elseif ($info['mime'] == 'image/gif') {
        imagegif($new_image, $destination);
    } elseif ($info['mime'] == 'image/png') {
        // PNG compression niveau 0-9 (0 = no compression, 9 = maximum compression)
        $png_quality = (int)($quality / 10) - 1;
        imagepng($new_image, $destination, $png_quality);
    }

    // Libérez la mémoire
    imagedestroy($image);
    imagedestroy($new_image);

    return $destination;
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
        // Utiliser le nom du bar pour créer le sous-dossier
        $bar_name = $_SESSION['bar_name']; // Assurez-vous que le nom du bar est stocké dans la session
        $target_dir = "uploads/" . $bar_name . "/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Ajouter un horodatage au nom du fichier pour le rendre unique
        $filename = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . $filename . '_' . time() . '.' . $imageFileType;
        
        $uploadOk = 1;
    
        // Vérifiez si l'image est une image réelle ou une fausse image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $message .= "Le fichier n'est pas une image. ";
            $uploadOk = 0;
        }
    
        // Vérifiez la taille du fichier
        if ($_FILES["image"]["size"] > 5000000) {
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
            $temp_file = $_FILES["image"]["tmp_name"];
            if (move_uploaded_file($temp_file, $target_file)) {
                // Compresser et redimensionner l'image
                $compressed_file = $target_dir . 'compressed_' . $filename . '_' . time() . '.' . $imageFileType;
                compressAndResizeImage($target_file, $compressed_file, 65, 800, 800); // Compresser à 65% de qualité et redimensionner à 800x800
                $image = $compressed_file;
    
                // Supprimer le fichier original non compressé
                unlink($target_file);
    
                //$message .= "Le fichier " . htmlspecialchars(basename($_FILES["image"]["name"])) . " a été téléchargé, redimensionné et compressé avec succès.";
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
