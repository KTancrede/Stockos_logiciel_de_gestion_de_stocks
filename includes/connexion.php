<?php
if (!function_exists('connectDB')) {
    function connectDB($dbname = 'master_db') {
        $host = 'localhost';
        $username = 'tanc';  // Changez par votre nom d'utilisateur MySQL
        $password = 'JmdNaClmd24';  // Changez par votre mot de passe MySQL

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            return null;
        }
    }
}
?>
