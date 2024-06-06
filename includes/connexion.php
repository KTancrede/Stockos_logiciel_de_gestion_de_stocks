<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('connectDB')) {
    function connectDB($dbname = null, $username = 'tanc', $password = 'JmdNaClmd24') {
        $host = 'localhost';

        session_start();
        if ($dbname === null) {
            if (isset($_SESSION['db_name'])) {
                $dbname = $_SESSION['db_name'];
            } else {
                $dbname = 'master_db'; // Default to master_db if no specific database is set
            }
        }

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            return null;
        }
    }
}
?>
