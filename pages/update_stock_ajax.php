<?php
include '../includes/connexion.php';

// Vérifiez si les données POST existent
if (isset($_POST['id']) && isset($_POST['enStock'])) {
    $id = $_POST['id'];
    $enStock = $_POST['enStock'];

    // Fonction pour mettre à jour le stock
    function updateProductStock($id, $enStock) {
        $conn = connectDB();
        $sql = "UPDATE produits SET enStock = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$enStock, $id]);
    }

    // Mettez à jour le stock
    updateProductStock($id, $enStock);
}
?>
