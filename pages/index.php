<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/connexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["login"]) && isset($_POST["mot_de_passe"])) {
    $login = htmlspecialchars($_POST['login']);
    $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);

    $conn = connectDB();
    if ($conn) {
        try {
            $sql = "SELECT id, mot_de_passe FROM utilisateurs WHERE login = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $login);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: page_acceuil.php");
                exit();
            } else {
                $message = "Identifiant ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $message = "Erreur: " . $e->getMessage();
        }
    } else {
        $message = "Erreur de connexion à la base de données.";
    }
}
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML Basic 1.1//EN' 'http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Connexion</title>
</head>
<header>
    <a href='page_acceuil.php' class='logo'>
        Stockos
    </a>
</header>
<body>
<div class='login-container'>
    <form class='form_login' method='POST' action=''>
        <h2>Connexion</h2>
        <label for='login'>Identifiant</label>
        <input type='text' name='login' id='login' required autocomplete='off'/><br>
        <label for='mot_de_passe'>Mot de passe</label>
        <input type='password' name='mot_de_passe' id='mot_de_passe' required autocomplete='off'/><br>
        <input type='submit' value='Connexion'/>
    </form>
    <div id='error_message'>
        <?php if ($message) echo $message; ?>
    </div>
</div>
</body>
</html>
