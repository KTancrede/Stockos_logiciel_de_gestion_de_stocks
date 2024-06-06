<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/verifier_connexion.php';
verifier_connexion();
$message = "";

function getUserRole() {
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
    $conn = connectDB();

    $nom = $_POST['nom'];
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash du mot de passe
    $role = $_POST['role'];

    // Vérifier si le login existe déjà dans la base de données spécifique au bar
    $sql_check = "SELECT COUNT(*) FROM utilisateurs WHERE login = :login";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':login', $login);
    $stmt_check->execute();
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        $message = "Erreur : Le login existe déjà.";
    } else {
        // Ajout dans la base de données spécifique au bar
        $sql = "INSERT INTO utilisateurs (login, mot_de_passe, role, nom) VALUES (:login, :mot_de_passe, :role, :nom)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':mot_de_passe', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':nom', $nom);

        if ($stmt->execute()) {
            // Ajout dans la base de données master_db
            $master_conn = connectDB('yfyqgdsu_master_db'); // Connectez-vous à la base de données master_db
            $sql_master = "INSERT INTO bars (name, login, mot_de_passe, db_name) VALUES (:name, :login, :mot_de_passe, :db_name)";
            $stmt_master = $master_conn->prepare($sql_master);
            $stmt_master->bindParam(':name', $nom);
            $stmt_master->bindParam(':login', $login);
            $stmt_master->bindParam(':mot_de_passe', $password);
            $stmt_master->bindParam(':db_name', $_SESSION['db_name']);

            if ($stmt_master->execute()) {
                $message = "Utilisateur ajouté avec succès.";
            } else {
                $message = "Erreur lors de l'ajout de l'utilisateur dans master_db: " . implode(", ", $stmt_master->errorInfo());
            }
        } else {
            $message = "Erreur lors de l'ajout de l'utilisateur: " . implode(", ", $stmt->errorInfo());
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
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8'/>
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
                    <option value="responsable">Responsable</option>
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
        }, 5000);
    });
    </script>
</body>
</html>
