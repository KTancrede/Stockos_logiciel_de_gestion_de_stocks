<?php
session_start();

function connectDB() {
    $servername = "localhost";
    $username = "tanc";
    $password = "JmdNaClmd24";
    $dbname = "stockos";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Configure PDO pour qu'il lance des exceptions en cas d'erreur
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
        return null;
    }
}

function connexion($login, $mot_de_passe) {
    if(empty($login) || empty($mot_de_passe)) {
        // Identifiants non fournis, connexion échouée
        return false;
    }
    $conn = connectDB();
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $mot_de_passe === $user['mot_de_passe']) {
        //if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) { //Connexion hashed
            // Connexion réussie
            
            $_SESSION['user_id'] = $user['id'];
            return true; // Indiquer une connexion réussie
        } else {
            // Identifiants incorrects
            return false; // Indiquer des identifiants incorrects
        }
    }
}

?>