<?php
include '../includes/verifier_connexion.php';
verifier_connexion();
$message = "";

function getUserRole() {
    // Remplacez ceci par votre logique pour obtenir le rôle de l'utilisateur connecté
    // Par exemple, vous pouvez récupérer le rôle à partir de la session ou de la base de données
    return $_SESSION['user_role']; // Exemple : 'patron', 'manager', 'employe'
}

$userRole = getUserRole();

// Rediriger les utilisateurs non autorisés
if ($userRole !== 'patron') {
    header('Location: page_acceuil.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/connexion.php';

    // Vérification des variables de session nécessaires
    if (!isset($_SESSION['bar_name']) || !isset($_SESSION['db_name'])) {
        $message = "Les informations du bar sont manquantes.";
    } else {
        $nom = $_POST['nom'];
        $login = $_POST['login'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash du mot de passe
        $role = $_POST['role'];

        // Connexion à la base de données du bar
        $conn = connectDB();

        try {
            // Insertion dans la base de données du bar
            $sql = "INSERT INTO utilisateurs (login, mot_de_passe, role, nom) VALUES (:login, :mot_de_passe, :role, :nom)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':mot_de_passe', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':nom', $nom);

            if ($stmt->execute()) {
                // Connexion à la base de données master_db
                $master_conn = connectDB('master_db');

                // Récupération des informations de session
                $bar_name = $_SESSION['bar_name'];
                $db_name = $_SESSION['db_name'];

                // Insertion dans master_db (table bars)
                $master_sql = "INSERT INTO bars (name, db_name, login, mot_de_passe) VALUES (:name, :db_name, :login, :mot_de_passe)";
                $master_stmt = $master_conn->prepare($master_sql);
                $master_stmt->bindParam(':name', $bar_name);
                $master_stmt->bindParam(':db_name', $db_name);
                $master_stmt->bindParam(':login', $login);
                $master_stmt->bindParam(':mot_de_passe', $password);

                if ($master_stmt->execute()) {
                    $message = "Utilisateur ajouté avec succès.";
                } else {
                    $message = "Erreur lors de l'ajout de l'utilisateur dans master_db: " . implode(", ", $master_stmt->errorInfo());
                }
            } else {
                $message = "Erreur lors de l'ajout de l'utilisateur dans la base de données du bar: " . implode(", ", $stmt->errorInfo());
            }
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML Basic 1.1//EN'
 'http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/>
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Ajouter un employé - Stockos</title>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>
    <main>
        <div class="form_ajouter_container">
            <h2>Ajouter un nouvel employé ou manager</h2>
            <form method="POST" action="ajout_employe.php">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required><br>
                <label for="login">Login</label>
                <input type="text" name="login" id="login" required><br>
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required><br>
                <label for="role">Rôle</label>
                <select name="role" id="role" required>
                    <option value="" disabled selected>Choisir un rôle</option>
                    <option value="employe">Employé</option>
                    <option value="manager">Manager</option>
                </select><br>
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
        }, 9000); 
    });
    </script>
</body>
</html>
