<?php


include '../includes/connexion.php';
include '../includes/verifier_connexion.php';
verifier_connexion();

$message = "";

// Fonction pour récupérer les informations du bar
function getBarInfo() {
    $conn = connectDB('master_db');  // Connectez-vous à la base de données master_db pour récupérer les informations du bar
    $sql = "SELECT * FROM bars WHERE db_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['db_name']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les informations des utilisateurs
function getUsersInfo() {
    $conn = connectDB($_SESSION['db_name']);  // Connectez-vous à la base de données spécifique au bar pour récupérer les informations des utilisateurs
    $sql = "SELECT * FROM utilisateurs";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$barInfo = getBarInfo();
$usersInfo = getUsersInfo();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mise à jour des informations du bar
    if (isset($_POST['update_bar'])) {
        $barName = htmlspecialchars($_POST['bar_name']);
        $login = htmlspecialchars($_POST['bar_login']);
        $mot_de_passe = !empty($_POST['bar_mot_de_passe']) ? password_hash($_POST['bar_mot_de_passe'], PASSWORD_DEFAULT) : $barInfo['mot_de_passe'];

        $conn = connectDB('master_db');  // Connectez-vous à la base de données master_db pour mettre à jour les informations du bar
        $sql = "UPDATE bars SET name = ?, login = ?, mot_de_passe = ? WHERE db_name = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$barName, $login, $mot_de_passe, $_SESSION['db_name']])) {
            $message = "Informations du bar mises à jour avec succès.";
            $barInfo = getBarInfo(); // Rafraîchir les informations du bar
        } else {
            $message = "Erreur lors de la mise à jour des informations du bar.";
        }
    }

    // Mise à jour des informations des utilisateurs
    if (isset($_POST['update_user'])) {
        $userId = htmlspecialchars($_POST['user_id']);
        $login = htmlspecialchars($_POST['user_login']);
        $role = htmlspecialchars($_POST['user_role']);
        $nom = htmlspecialchars($_POST['user_nom']);
        $prenom = htmlspecialchars($_POST['user_prenom']);
        $email = htmlspecialchars($_POST['user_email']);
        $mot_de_passe = !empty($_POST['user_mot_de_passe']) ? password_hash($_POST['user_mot_de_passe'], PASSWORD_DEFAULT) : null;

        $conn = connectDB($_SESSION['db_name']);  // Connectez-vous à la base de données spécifique au bar pour mettre à jour les informations des utilisateurs
        $sql = "UPDATE utilisateurs SET login = ?, role = ?, nom = ?, prenom = ?, email = ?" . ($mot_de_passe ? ", mot_de_passe = ?" : "") . " WHERE id = ?";
        $params = [$login, $role, $nom, $prenom, $email];
        if ($mot_de_passe) {
            $params[] = $mot_de_passe;
        }
        $params[] = $userId;

        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            $message = "Informations de l'utilisateur mises à jour avec succès.";
            $usersInfo = getUsersInfo(); // Rafraîchir les informations des utilisateurs
        } else {
            $message = "Erreur lors de la mise à jour des informations de l'utilisateur.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon/favicon.ico"/>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>Réglages - Stockos</title>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="page_acceuil.php" class="logo">
                Stockos
            </a>
            <a href="reglages.php" class="settings-link">
                <img class='img-reg' src='../assets/images/logo_reglage.webp' alt='Paramètres'/>
            </a>
        </div>
    </header>
    <main>
        <div class="form_ajouter_container">
            <h2>Informations des Utilisateurs</h2>
            <?php foreach ($usersInfo as $user) : ?>
                <form method="POST" action="reglages.php">
                    <input type="hidden" name="update_user" value="1">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <label for="user_login_<?php echo $user['id']; ?>">Identifiant</label>
                    <input type="text" name="user_login" id="user_login_<?php echo $user['id']; ?>" value="<?php echo htmlspecialchars($user['login']); ?>" required><br>
                    <label for="user_role_<?php echo $user['id']; ?>">Rôle</label>
                    <select name="user_role" id="user_role_<?php echo $user['id']; ?>" required>
                        <option value="patron" <?php if ($user['role'] == 'patron') echo 'selected'; ?>>Patron</option>
                        <option value="employe" <?php if ($user['role'] == 'employe') echo 'selected'; ?>>Employé</option>
                        <option value="responsable" <?php if ($user['role'] == 'responsable') echo 'selected'; ?>>Responsable</option>
                    </select><br>
                    <label for="user_nom_<?php echo $user['id']; ?>">Nom</label>
                    <input type="text" name="user_nom" id="user_nom_<?php echo $user['id']; ?>" value="<?php echo htmlspecialchars($user['nom']); ?>" required><br>
                    <label for="user_prenom_<?php echo $user['id']; ?>">Prénom</label>
                    <input type="text" name="user_prenom" id="user_prenom_<?php echo $user['id']; ?>" value="<?php echo htmlspecialchars($user['prenom']); ?>" required><br>
                    <label for="user_email_<?php echo $user['id']; ?>">Email</label>
                    <input type="email" name="user_email" id="user_email_<?php echo $user['id']; ?>" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
                    <label for="user_mot_de_passe_<?php echo $user['id']; ?>">Mot de passe (laissez vide pour ne pas changer)</label>
                    <input type="password" name="user_mot_de_passe" id="user_mot_de_passe_<?php echo $user['id']; ?>"><br>
                    <input type="submit" value="Mettre à jour l'utilisateur">
                </form>
            <?php endforeach; ?>

            <?php
            if ($message) {
                echo "<div id='msg_ajout'>$message</div>";
            }
            ?>
        </div>
    </main>
</body>
</html>
