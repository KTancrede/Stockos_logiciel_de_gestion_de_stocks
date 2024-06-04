<?php
include '../includes/connexion.php';
include '../includes/verifier_connexion.php';
verifier_connexion();

// Fonction pour récupérer tous les fournisseurs
function getAllFournisseur() {
    $conn = connectDB();
    $sql = "SELECT * FROM fournisseur";
    $stmt = $conn->query($sql);
    $fournisseur = [];
    
    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fournisseur[] = $row;
        }
    }
    
    return $fournisseur;
}

// Fonction pour supprimer les fournisseurs sélectionnés
function supprimer_fournisseur($noms) {
    $conn = connectDB();
    $placeholders = implode(',', array_fill(0, count($noms), '?'));
    $sql = "DELETE FROM fournisseur WHERE nom IN ($placeholders)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($noms);
        echo "<div id='msg_ajout'>Fournisseur supprimé avec succès.</div>";
    } catch (PDOException $e) {
        echo "<div id='msg_ajout'>Erreur: " . $e->getMessage().'</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['noms'])) {
    $noms = $_POST['noms'];
    supprimer_fournisseur($noms);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Supprimer Fournisseur</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>
    <main>
        <div class="form_supprimer_container">
            <h2>Supprimer des fournisseurs</h2>
            <form method="POST" action="">
                <?php
                $fournisseurs = getAllFournisseur();
                if (count($fournisseurs) > 0) {
                    foreach ($fournisseurs as $fournisseur) {
                        echo '<div class="product-item">';
                        echo '<input type="checkbox" name="noms[]" value="' . htmlspecialchars($fournisseur['nom']) . '">';
                        echo '<label>' . htmlspecialchars($fournisseur['nom']) . '</label>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Aucun fournisseur trouvé.</p>';
                }
                ?>
                <input type="submit" value="Supprimer">
            </form>
        </div>
    </main>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            var msg = document.getElementById('msg_ajout');
            if (msg) {
                msg.style.display = 'none';
            }
        }, 3000); 
    });
    </script>
</body>
</html>
