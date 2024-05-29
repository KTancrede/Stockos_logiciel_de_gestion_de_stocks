<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connexion.php';

// Connexion à la base de données
$conn = connectDB();
if(!$conn) {
    // Si la connexion à la base de données échoue, afficher un message d'erreur
    die("Erreur de connexion à la base de données.");
}

$html = "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML Basic 1.1//EN'
 'http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
    <link rel='stylesheet' href='style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Connexion</title>
</head>
<body>
<div class='container'>
    <form class='form_login' method='POST'>
        <h2>Connexion</h2>
        <label for='login'>Identifiant</label>
        <input type='text' name='login' id='login' required autocomplete='off'/><br>
        <label for='mot_de_passe'>Mot de passe</label>
        <input type='password' name='mot_de_passe' id='mot_de_passe' required autocomplete='off'/><br>
        <input type='submit' value='Connexion'/>
    </form>
    <div id='error_message'>%s</div> <!-- Emplacement pour afficher les messages d'erreur -->
</div>
</body>
</html>
";

$message = '';
if(isset($_POST["login"]) AND isset($_POST["mot_de_passe"])){
    $resultat = connexion($_POST["login"],$_POST["mot_de_passe"]);
    if($resultat === true){
        
        // Si les identifiants sont corrects, affichez un message de succès
        header("Location: page_acceuil.php");
        $message = "Connexion réussie !";
    } else {
        // Si les identifiants sont incorrects, affichez un message d'erreur
        $message = "Identifiant ou mot de passe incorrect.";
    }
    printf($html, $message);
}else{
    printf($html, "");
}


?>
