<?php
include_once '../includes/connexion.php';

function createBarDatabase($barName, $dbName) {
    $conn = connectDB();
    if ($conn) {
        try {
            echo "Creating database: $dbName\n"; // Debug message
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
                    enStock DECIMAL(10, 2)  NULL,
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
            echo "Erreur: " . $e->getMessage(); // Debug message
            return false;
        }
    } else {
        echo "Erreur de connexion à la base de données."; // Debug message
        return false;
    }
}

function createBarAccount($barName, $login, $mot_de_passe) {
    $conn = connectDB();
    if ($conn) {
        try {
            // Vérifier si le login existe déjà
            $sql = "SELECT COUNT(*) FROM bars WHERE login = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$login]);
            if ($stmt->fetchColumn() > 0) {
                echo "Erreur: Un bar avec ce login existe déjà.\n"; // Debug message
                return false;
            }

            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $dbName = "bar_" . strtolower(preg_replace("/[^a-zA-Z0-9]+/", "_", $barName));

            echo "Inserting bar info into master_db\n"; // Debug message
            $sql = "INSERT INTO bars (name, db_name, login, mot_de_passe) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $barName);
            $stmt->bindParam(2, $dbName);
            $stmt->bindParam(3, $login);
            $stmt->bindParam(4, $hashed_password);

            if ($stmt->execute()) {
                echo "Creating specific database for the bar: $dbName\n"; // Debug message
                if (createBarDatabase($barName, $dbName)) {
                    $barConn = connectDB($dbName, 'tanc', 'JmdNaClmd24'); // Using connectDB function
                    if ($barConn) {
                        $sql = "INSERT INTO utilisateurs (login, mot_de_passe, role) VALUES (?, ?, 'patron')";
                        $stmt = $barConn->prepare($sql);
                        $stmt->bindParam(1, $login);
                        $stmt->bindParam(2, $hashed_password);
                        if ($stmt->execute()) {
                            echo "Bar account and patron user created successfully\n"; // Debug message
                            return true;
                        } else {
                            echo "Erreur lors de la création de l'utilisateur patron.\n"; // Debug message
                            return false;
                        }
                    } else {
                        echo "Erreur de connexion à la nouvelle base de données.\n"; // Debug message
                        return false;
                    }
                } else {
                    echo "Erreur lors de la création de la base de données du bar.\n"; // Debug message
                    return false;
                }
            } else {
                echo "Erreur: " . implode(", ", $stmt->errorInfo()) . "\n"; // Debug message
                return false;
            }
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage() . "\n"; // Debug message
            return false;
        }
    } else {
        echo "Erreur de connexion à la base de données.\n"; // Debug message
        return false;
    }
}
?>
