<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/connexion.php';
include_once 'creationDB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barName = htmlspecialchars($_POST['bar_name']);
    $login = htmlspecialchars($_POST['login']);
    $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);

    echo "Creating bar account for: $barName, $login"; // Debug message

    if (createBarAccount($barName, $login, $mot_de_passe)) {
        echo "Bar account created successfully.";
    } else {
        echo "Failed to create bar account.";
    }
} else {
    echo "No POST data received.";
}
?>
