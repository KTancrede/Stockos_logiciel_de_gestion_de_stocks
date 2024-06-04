<?php 
include '../includes/connexion.php';
include '../includes/verifier_connexion.php';
verifier_connexion();

$message="";

function ajouter_fournisseur($nom,$email,$numero){
    global $message;
    $conn=connectDB();
    if($conn){
        try{
            $sql="INSERT INTO fournisseur (nom, email, numero_telephone) VALUES (?, ?, ?)";
            $stmt=$conn->prepare($sql);

            $stmt->bindParam(1, $nom);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $numero);

            if($stmt->execute()){
                $message="Fournisseur ajouté avec succès";
            }else{
                echo "Erreur: ". implode(", ",$stmt->errorInfo());
            }
        }catch(PDOException $e){
            $message = "Erreur: " . $e->getMessage();
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $email=htmlspecialchars($_POST['email']);
    $numero=htmlspecialchars($_POST['numero']);
    ajouter_fournisseur($nom,$email,$numero);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Ajouter Fournisseur</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>
    <main>
        <div class="form_ajouter_container">
            <h2>Ajouter un fournisseur</h2>
            <form method="POST" action="">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required><br>
                <label for="email">Email</label>
                <input type="text" name="email" id="email" required><br>
                <label for="numero_tel">Numéro de Telephone</label>
                <input type="text" name="numero" id="numero" required><br>
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
        }, 3000); 
    });
    </script>
</body>
</html>
    