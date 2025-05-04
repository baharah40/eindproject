<?php
include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT gebruikersnaam, is_admin FROM gebruikers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    $user = null;
}

// Haal producten op
$sql = "
    SELECT p.id AS product_id, p.naam, p.prijs, 
           (SELECT afbeelding FROM product_afbeeldingen WHERE product_id = p.id LIMIT 1) AS eerste_afbeelding
    FROM producten p
    ORDER BY p.id DESC
";
$result = $conn->query($sql);

// Maak array
$producten = [];
while ($row = $result->fetch_assoc()) {
    $producten[] = [
        'id' => $row['product_id'],
        'naam' => $row['naam'],
        'prijs' => $row['prijs'],
        'afbeelding' => $row['eerste_afbeelding']
    ];
}

// Haal social media op
$socialMediaLinks = $conn->query("SELECT name, link FROM social_media_links");
?>

<!DOCTYPE html>
<html>
<head>
    <title>CarryChic</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* CSS blijft exact zoals je gaf, geen wijzigingen */
     /* Social Media Links Styling */
     .social-media-links {
            padding: 20px;
            text-align: center;
        }

        .footer-container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            padding: 20px;
        }

        .footer-section {
            flex: 1;
            padding: 10px;
            max-width: 300px;
        }

        .footer-section h2 {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .social-media-links ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-media-links ul li {
            display: inline;
        }

        .social-media-links a {
            text-decoration: none;
            color: rgb(254, 254, 254);
            font-weight: bold;
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .social-media-links a:hover {
            background-color: #007bff;
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }

        /* Knoppen naast elkaar */
        .product-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        /* Winkelwagen knop styling */
        .add-to-cart-button {
            display: inline-block;
            padding: 10px;
            color: black;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .add-to-cart-button i {
            margin-right: 5px;
        }

        .add-to-cart-button:hover {
            background-color: #0056b3;
        }

        /* Wishlist knop styling */
        .wishlist-button {
            display: inline-block;
            padding: 10px;
            color: black;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .wishlist-button i {
            margin-right: 5px;
        }

        .wishlist-button:hover {
            background-color:rgb(171, 163, 163);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-left">
            <?php if ($user): ?>
                <p>Welkom, <?php echo htmlspecialchars($user['gebruikersnaam']); ?>!</p>
            <?php endif; ?>
        </div>
        <div class="header-right">
            <a href='winkelwagen.php' class='cart-button'>Winkelwagen <i class='fas fa-shopping-cart'></i></a>
            <a href='wishlist.php' class='cart-button'>Wishlist <i class='fas fa-heart'></i></a>
            <?php if ($user && $user['is_admin']): ?>
                <form method='GET' action='admin.php' style='display: inline;'>
                    <button type='submit' class='admin-button'>Admin Panel</button>
                </form>
            <?php endif; ?>
            <?php if ($user): ?>
                <form method='POST' action='logout.php' style='display: inline;'>
                    <button type='submit' class='logout-button'>Uitloggen</button>
                </form>
            <?php else: ?>
                <a href='login.php' class='login-button'>Inloggen</a>
            <?php endif; ?>
        </div>
    </div>

    <h1>CarryChic</h1>
    <div class='product-list'>
        <?php foreach ($producten as $product): ?>
            <div class='product-item'>
                <a href='product_detail.php?id=<?= $product['id'] ?>'>
                    <?php if (!empty($product['afbeelding'])): ?>
                        <img src='images/<?= htmlspecialchars($product['afbeelding']) ?>' alt='<?= htmlspecialchars($product['naam']) ?>'>
                    <?php else: ?>
                        <img src='images/placeholder.png' alt='Geen afbeelding'>
                    <?php endif; ?>
                    <div class='product-info'>
                        <h2><?= htmlspecialchars($product['naam']) ?></h2>
                        <p>€<?= htmlspecialchars($product['prijs']) ?></p>
                    </div>
                </a>
                <div class='product-buttons'>
                    <a href='winkelwagen.php?id=<?= $product['id'] ?>' class='add-to-cart-button'>
                        <i class='fas fa-shopping-cart'></i>
                    </a>
                    <a href='wishlist.php?id=<?= $product['id'] ?>' class='wishlist-button'>
                        <i class='fas fa-heart'></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'footer.php'; ?>


</body>
</html>
