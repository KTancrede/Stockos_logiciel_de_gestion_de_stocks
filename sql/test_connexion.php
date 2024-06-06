<?php
$host = 'localhost';
$dbname = 'yfyqgdsu_master_db';
$username = 'yfyqgdsu_tanc';
$password = 'JmdNaClmd24';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>
