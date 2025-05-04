<?php
include 'connect.php';
session_start();

// Voeg product toe aan winkelwagen
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (!isset($_SESSION['winkelwagen'])) {
        $_SESSION['winkelwagen'] = array();
    }
    if (isset($_SESSION['winkelwagen'][$id])) {
        $_SESSION['winkelwagen'][$id]++;
    } else {
        $_SESSION['winkelwagen'][$id] = 1;
    }
}

// Verwijder product uit winkelwagen
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    if (isset($_SESSION['winkelwagen'][$remove_id])) {
        if ($_SESSION['winkelwagen'][$remove_id] > 1) {
            $_SESSION['winkelwagen'][$remove_id]--;
        } else {
            unset($_SESSION['winkelwagen'][$remove_id]);
        }
    }
}

// Haal producten op uit winkelwagen
$producten_in_winkelwagen = array();
$totaal_prijs = 0;

if (!empty($_SESSION['winkelwagen'])) {
    foreach ($_SESSION['winkelwagen'] as $product_id => $aantal) {
        // Haal productgegevens op
        $sql = "SELECT id, naam, prijs FROM producten WHERE id = $product_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $product['aantal'] = $aantal;

            // Haal de eerste afbeelding op uit product_afbeeldingen
            $img_sql = "SELECT afbeelding FROM product_afbeeldingen WHERE product_id = $product_id LIMIT 1";
            $img_result = $conn->query($img_sql);
            if ($img_result->num_rows > 0) {
                $img_row = $img_result->fetch_assoc();
                $product['afbeelding'] = $img_row['afbeelding'];
            } else {
                $product['afbeelding'] = 'placeholder.png'; // Fallback afbeelding
            }

            $producten_in_winkelwagen[] = $product;
            $totaal_prijs += $product['prijs'] * $aantal;
        }
    }
}
else {
    $producten_in_winkelwagen = array();
}
// Sluit de databaseverbinding
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winkelwagen</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .product-list {
            list-style-type: none;
            padding: 0;
        }

        .product-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .product-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 5px;
        }

        .product-info {
            flex: 1;
        }

        .product-info h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .product-info p {
            margin: 5px 0;
            color: #777;
        }

        .product-quantity {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .remove-button {
            background-color: rgba(231, 77, 60, 0.52);
            color: white;
            border: none;
            padding: 5px 7px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 3px;
            text-decoration: none;
        }

        .remove-button:hover {
            background-color: rgb(224, 23, 0);
        }

        .continue-shopping{
    display: block;
    margin: 30px auto 0;
    padding: 12px 24px;
    background-color: #111;
    color: white;
    text-align: center;
    border-radius: 6px;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 500;
    text-decoration: none;
    transition: background-color 0.2s ease;
    width: fit-content;
}
      .continue-shopping:hover {
            background-color: #333;
        }

        .total-price {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Winkelwagen</h1>
        <ul class="product-list">
            <?php if (!empty($producten_in_winkelwagen)): ?>
                <?php foreach ($producten_in_winkelwagen as $product): ?>
                    <li class="product-item">
                        <img src="images/<?php echo $product['afbeelding']; ?>" alt="<?php echo $product['naam']; ?>">
                        <div class="product-info">
                            <h2><?php echo $product['naam']; ?></h2>
                            <p>€<?php echo number_format($product['prijs'], 2); ?></p>
                            <span class="product-quantity">Aantal: x<?php echo $product['aantal']; ?></span>
                        </div>
                        <a href="winkelwagen.php?remove=<?php echo $product['id']; ?>" class="remove-button">Verwijderen</a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Uw winkelwagen is leeg</p>
            <?php endif; ?>
        </ul>

        <?php if (!empty($producten_in_winkelwagen)): ?>
            <div class="total-price">
                Totaalprijs: €<?php echo number_format($totaal_prijs, 2); ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="continue-shopping">Verder winkelen</a>
    </div>
</body>
</html>
