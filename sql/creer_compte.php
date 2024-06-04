<?php
include 'connexion.php';

function creer_compte_utilisateur($login, $mot_de_passe) {
    $conn = connectDB();
    if ($conn) {
        try {
            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT); // Hachage du mot de passe
            $sql = "INSERT INTO utilisateurs (login, mot_de_passe) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $login);
            $stmt->bindParam(2, $hashed_password);

            if ($stmt->execute()) {
                echo "Utilisateur ajouté avec succès\n";
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

// Paramètres fixes pour créer un compte utilisateur
$login = 'tanc';
$mot_de_passe = 'password_hashed1';

creer_compte_utilisateur($login, $mot_de_passe);
?>
