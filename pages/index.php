<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/connexion.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: page_acceuil.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["login"]) && isset($_POST["mot_de_passe"])) {
    $login = htmlspecialchars($_POST['login']);
    $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);

    $conn = connectDB();
    if ($conn) {
        $sql = "SELECT * FROM bars WHERE login = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $login);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $bar = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifier le mot de passe
            if (password_verify($mot_de_passe, $bar['mot_de_passe'])) {
                $dbName = $bar['db_name'];
                $userConn = connectDB($dbName);

                if ($userConn) {
                    // Vérifier les informations utilisateur dans la base de données du bar
                    $sql = "SELECT * FROM utilisateurs WHERE login = ?";
                    $stmt = $userConn->prepare($sql);
                    $stmt->bindParam(1, $login);
                    $stmt->execute();

                    if ($stmt->rowCount() == 1) {
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_login'] = $user['login'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['db_name'] = $dbName;

                        header("Location: page_acceuil.php");
                        exit();
                    } else {
                        $message = "Utilisateur non trouvé dans la base de données du bar.";
                    }
                } else {
                    $message = "Erreur de connexion à la base de données du bar.";
                }
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Identifiant non trouvé.";
        }
    } else {
        $message = "Erreur de connexion à la base de données.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/>
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8'/>
    <title>Connexion</title>
</head>
<body>
<header>
    <a href='page_acceuil.php' class='logo'>
        Stockos
    </a>
</header>
<div class='login-container'>
    <form class='form_login' method='POST' action=''>
        <h2>Connexion</h2>
        <label for='login'>Identifiant</label>
        <input type='text' name='login' id='login' required autocomplete='off'/><br>
        <label for='mot_de_passe'>Mot de passe</label>
        <input type='password' name='mot_de_passe' id='mot_de_passe' required autocomplete='off'/><br>
        <input type='submit' value='Connexion'/>
    </form>
    <div id='error_message'><?php echo $message; ?></div>
</div>
</body>
</html>
