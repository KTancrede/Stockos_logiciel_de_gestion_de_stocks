<?php
include 'connexion.php';
function createBarDatabase($barName, $dbName) {
    $conn = connectDB();
    if ($conn) {
        try {
            $conn->exec("CREATE DATABASE `$dbName`");
            $conn->exec("USE `$dbName`");

            // Créez les tables nécessaires dans la nouvelle base de données
            $conn->exec("
                CREATE TABLE produits (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nom VARCHAR(255) NOT NULL,
                    type VARCHAR(50) NOT NULL,
                    marque VARCHAR(50) NOT NULL,
                    quantite DECIMAL(10, 2) NOT NULL,
                    fournisseur VARCHAR(50) NOT NULL,
                    prix DECIMAL(10, 2) NOT NULL,
                    quantite_max DECIMAL(10, 2) NOT NULL,
                    enStock DECIMAL(10, 2) NOT NULL,
                    image VARCHAR(255) DEFAULT NULL
                )
            ");
            $conn->exec("
                CREATE TABLE fournisseur (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nom VARCHAR(255) NOT NULL,
                    email VARCHAR(255),
                    numero_telephone VARCHAR(20)
                )
            ");
            $conn->exec("
                CREATE TABLE utilisateurs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    login VARCHAR(50) NOT NULL UNIQUE,
                    mot_de_passe VARCHAR(255) NOT NULL,
                    role ENUM('patron', 'employe', 'responsable') NOT NULL,
                    nom VARCHAR(100),
                    prenom VARCHAR(100),
                    email VARCHAR(100) UNIQUE,
                    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            return true;
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    } else {
        echo "Erreur de connexion à la base de données.";
        return false;
    }
}

function createBarAccount($barName, $login, $mot_de_passe) {
    $conn = connectDB();
    if ($conn) {
        try {
            // Hachage du mot de passe
            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Nom de la nouvelle base de données
            $dbName = "bar_" . strtolower(preg_replace("/[^a-zA-Z0-9]+/", "_", $barName));

            // Insérer les informations du bar dans la base de données maître
            $sql = "INSERT INTO bars (name, db_name, login, mot_de_passe) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $barName);
            $stmt->bindParam(2, $dbName);
            $stmt->bindParam(3, $login);
            $stmt->bindParam(4, $hashed_password);

            if ($stmt->execute()) {
                // Créer une base de données spécifique pour ce bar
                if (createBarDatabase($barName, $dbName)) {
                    // Ajouter l'utilisateur patron dans la base de données spécifique
                    $barConn = new PDO("mysql:host=localhost;dbname=$dbName;charset=utf8", 'utilisateur', 'mot_de_passe');
                    $barConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = "INSERT INTO utilisateurs (login, mot_de_passe, role) VALUES (?, ?, 'patron')";
                    $stmt = $barConn->prepare($sql);
                    $stmt->bindParam(1, $login);
                    $stmt->bindParam(2, $hashed_password);
                    if ($stmt->execute()) {
                        echo "Compte bar et utilisateur patron créés avec succès.";
                    } else {
                        echo "Erreur lors de la création de l'utilisateur patron.";
                    }
                } else {
                    echo "Erreur lors de la création de la base de données du bar.";
                }
            } else {
                echo "Erreur: " . implode(", ", $stmt->errorInfo());
            }
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }
    } else {
        echo "Erreur de connexion à la base de données.";
    }
}

// Paramètres fixes pour créer un compte bar (à remplacer par des données d'un formulaire)
$barName = 'Bar Test';
$login = 'bartest';
$mot_de_passe = 'motdepasse';

createBarAccount($barName, $login, $mot_de_passe);
?>
