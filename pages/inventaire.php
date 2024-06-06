<?php
include '../includes/connexion.php';
include '../includes/verifier_connexion.php';
verifier_connexion();

// Fonction pour récupérer tous les produits
function getAllProducts() {
    $conn = connectDB();
    $sql = "SELECT id, nom, type, enStock, image FROM produits";
    $stmt = $conn->query($sql);
    $products = [];

    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $row;
        }
    }

    return $products;
}

$products = getAllProducts();
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML Basic 1.1//EN'
 'http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='../assets/images/favicon/favicon.ico'/> 
    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
    <title>Inventaire</title>
    <script>
        let currentIndex = 0;
        const products = <?php echo json_encode($products); ?>;

        function showProduct(index) {
        const product = products[index];
        document.getElementById('product-title').innerText = product.nom + ' (' + product.type + ')';
        
        const productImage = document.getElementById('product-image');
        if (product.image) {
            productImage.src = product.image;
            productImage.alt = product.nom;
            productImage.style.display = 'block';
        } else {
            productImage.style.display = 'none';
        }

        document.getElementById('enStock').value = product.enStock;
        document.getElementById('product-id').value = product.id;

        document.querySelector('.back').style.display = index > 0 ? 'inline-block' : 'none';
        document.querySelector('.next').style.display = index < products.length - 1 ? 'inline-block' : 'none';
        document.querySelector('.fin').style.display = index === products.length - 1 ? 'inline-block' : 'none';
    }


        function navigate(direction) {
            // Sauvegarder le stock actuel avant de naviguer
            saveStock();

            currentIndex += direction;
            if (currentIndex < 0) currentIndex = 0;
            if (currentIndex >= products.length) currentIndex = products.length - 1;
            showProduct(currentIndex);
        }

        function saveStock() {
            const id = document.getElementById('product-id').value;
            const enStock = document.getElementById('enStock').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_stock_ajax.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log('Stock updated');
                }
            };
            xhr.send('id=' + id + '&enStock=' + enStock);
        }

        function finishInventory() {
            saveStock();
            alert('Inventaire terminé !');
            window.location.href = 'page_acceuil.php';
        }

        document.addEventListener('DOMContentLoaded', function() {
            showProduct(currentIndex);
        });

    </script>
</head>
<body>
    <header>
        <a href="page_acceuil.php" class="logo">
            Stockos
        </a>
    </header>
    <main>
        <div class="container">
            <div id="product-title" class="product-title"></div>
            <img id="product-image" class="product-image" src="" alt="">
            <form method="POST" action="inventaire.php">
                <div class="input-container">
                    <input type="number" name="enStock" id="enStock" required>
                </div>
                <input type="hidden" name="id" id="product-id">
                <div class="navigation-buttons">
                    <button type="button" class="back" onclick="navigate(-1)">&#8592; Retour</button>
                    <button type="button" class="next" onclick="navigate(1)">Suivant &#8594;</button>
                    <button type="button" class="fin" onclick="finishInventory()">Fin</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
