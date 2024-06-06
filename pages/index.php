<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/connexion.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: page_acceuil.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["login"]) && isset($_POST["mot_de_passe"])) {
    $login = htmlspecialchars($_POST['login']);
    $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);

    $conn = connectDB('master_db'); // Connect to master_db to verify bar login
    if ($conn) {
        $sql = "SELECT * FROM bars WHERE login = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $login);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $bar = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($mot_de_passe, $bar['mot_de_passe'])) {
                $dbName = $bar['db_name'];
                $_SESSION['db_name'] = $dbName; // Store the bar's database name in session
                $_SESSION['bar_name'] = $bar['name']; // Correctly set the bar name in session
                $userConn = connectDB($dbName); // Connect to the specific bar's database
                if ($userConn) {
                    $sql = "SELECT * FROM utilisateurs WHERE login = ?";
                    $stmt = $userConn->prepare($sql);
                    $stmt->bindParam(1, $login);
                    $stmt->execute();

                    if ($stmt->rowCount() == 1) {
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_login'] = $user['login'];
                        $_SESSION['user_role'] = $user['role'];

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
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/icons/apple-touch-icon.png">
    <link rel="icon" sizes="192x192" href="../assets/icons/android-chrome-192x192.png">
    <link rel="icon" sizes="512x512" href="../assets/icons/android-chrome-512x512.png">
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
