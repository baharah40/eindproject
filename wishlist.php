<?php
include 'connect.php';
session_start();

// Voeg product toe aan wishlist
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Beveiliging tegen SQL-injectie
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    if (!in_array($id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $id;
    }
}

// Verwijder product uit wishlist
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    if (($key = array_search($remove_id, $_SESSION['wishlist'])) !== false) {
        unset($_SESSION['wishlist'][$key]);
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
    }
}

// Haal producten op uit wishlist
$producten_in_wishlist = [];
$totaal_prijs = 0;

if (!empty($_SESSION['wishlist'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['wishlist']), '?'));
    $stmt = $conn->prepare("SELECT id, naam, prijs FROM producten WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($_SESSION['wishlist'])), ...$_SESSION['wishlist']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($product = $result->fetch_assoc()) {
        // Haal eerste afbeelding op uit product_afbeeldingen
        $img_stmt = $conn->prepare("SELECT afbeelding FROM product_afbeeldingen WHERE product_id = ? LIMIT 1");
        $img_stmt->bind_param('i', $product['id']);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        if ($img_row = $img_result->fetch_assoc()) {
            $product['afbeelding'] = $img_row['afbeelding'];
        } else {
            $product['afbeelding'] = 'placeholder.png'; // Fallback
        }

        $producten_in_wishlist[] = $product;
        $totaal_prijs += $product['prijs'];
    }
}
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wishlist</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Algemene styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 20px;
    color: #333;
}

/* Container styling */
.container {
    max-width: 800px;
    margin: 20px auto;
    background-color: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Titel styling */
h1 {
    font-size: 24px;
    color: #222;
    text-align: center;
    margin-bottom: 20px;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Productlijst styling */
.product-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 0;
    margin: 0;
    list-style: none;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 16px;
    justify-content: space-between;
    padding: 16px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    height: 120px;
    overflow: hidden;
}

.product-item:hover {
    background-color: #fafafa;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

/* Afbeelding styling */
.product-item img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ddd;
    transition: transform 0.2s ease;
}

.product-item img:hover {
    transform: scale(1.05);
}

/* Productinformatie styling */
.product-info {
    flex-grow: 1;
    max-width: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.product-info h2 {
    font-size: 16px;
    margin: 0;
    color: #333;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-info p {
    font-size: 14px;
    color: #777;
    margin: 4px 0 0;
}

/* Verwijder-knop styling */
.remove-button {
    background-color: #ff4d4f;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
    text-transform: uppercase;
}

.remove-button:hover {
    background-color: #d9363e;
}

/* Totaalprijs styling */
.total-price {
    font-size: 18px;
    font-weight: 600;
    text-align: right;
    margin-top: 20px;
    color: #444;
    padding-top: 10px;
    border-top: 2px solid #eee;
}

/* Home-knop styling */
.home-button {
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

.home-button:hover {
    background-color: #333;
}

/* Responsieve styling */
@media (max-width: 600px) {
    .product-item {
        flex-direction: column;
        height: auto;
        align-items: flex-start;
    }

    .product-item img {
        width: 100%;
        height: auto;
    }

    .product-info {
        max-width: 100%;
    }

    .remove-button {
        align-self: flex-end;
        margin-top: 10px;
    }
}

    </style>
</head>
<body>

<div class="container">
    <h1>Wishlist</h1>

    <ul class="product-list">
        <?php if (!empty($producten_in_wishlist)): ?>
            <?php foreach ($producten_in_wishlist as $product): ?>
                <li class="product-item">
                    <a href="product_detail.php?id=<?= $product['id']; ?>">
                        <img src="images/<?= htmlspecialchars($product['afbeelding']); ?>" alt="<?= htmlspecialchars($product['naam']); ?>">
                    </a>
                    <div class="product-info">
                        <h2><?= htmlspecialchars($product['naam']); ?></h2>
                        <p>€<?= number_format($product['prijs'], 2); ?></p>
                    </div>
                    <a href="wishlist.php?remove=<?= $product['id']; ?>" class="remove-button">
                        <i class="fas fa-trash"></i> Verwijderen
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #777;">Uw wishlist is leeg 😔</p>
        <?php endif; ?>
    </ul>

    <a href="index.php" class="home-button">Home</a>
</div>

</body>
</html>
