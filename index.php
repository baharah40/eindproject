<?php
include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT gebruikersnaam, is_admin, profiel_foto FROM gebruikers WHERE id = ?";
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

?>

<!DOCTYPE html>
<html>

<head>
    <title>CarryChic</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Favicon link -->
<link rel="icon" type="images/png" href="favicon.png" sizes="32x32">

    <style>


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
            background-color: rgb(171, 163, 163);
        }
        /* Profielfoto in header */
        .profielfoto-header {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            vertical-align: middle;
        }
        .nav-label {
    font-size: 11px;
    text-decoration: underline;
    margin-left: 4px;
    vertical-align: middle;
    color: inherit;
}
.cart-button, .logout-button, .login-button {
    display: inline-flex;
    align-items: center;
    gap: 1px;
}
.cart-button:hover,
.logout-button:hover,
.login-button:hover {
    background-color: transparent !important;
    cursor: pointer;
}
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <?php if ($user): ?>
                <div style="display:flex; align-items:center; gap:10px;">
                    <?php if (!empty($user['profiel_foto'])): ?>
                        <img src="profile_images/<?= htmlspecialchars($user['profiel_foto']) ?>" alt="profile_foto" class="profielfoto-header" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    <?php endif; ?>
                    <p>Welkom, <?= htmlspecialchars($user['gebruikersnaam']); ?>!</p>
                </div>
                <?php endif; ?>
            </div>
           <div class="header-right">
    <a href="index.php" class="cart-button">
        <i class="fas fa-home"></i>
        <span class="nav-label">Home</span>
    </a>
    <a href="account_aanmak.php" class="cart-button">
        <i class="fas fa-user"></i>
        <span class="nav-label">Account</span>
    </a>
    <a href="winkelwagen.php" class="cart-button">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-label">Winkelwagen</span>
    </a>
    <a href="wishlist.php" class="cart-button">
        <i class="fas fa-heart"></i>
        <span class="nav-label">Wishlist</span>
    </a>
    <?php if ($user && $user['is_admin']): ?>
        <form method='GET' action='admin.php' style='display:inline;'>
            <button type='submit' class='cart-button' title='Admin' style='background:none; border:none; cursor:pointer;'>
                <i class='fas fa-user-shield'></i>
                <span class="nav-label">Admin</span>
            </button>
        </form>
    <?php endif; ?>
    <?php if ($user): ?>
        <form method='POST' action='logout.php' style='display:inline;'>
            <button type='submit' class='logout-button' title='Uitloggen' style='background:none; border:none; cursor:pointer;'>
                <i class='fas fa-sign-out-alt'></i>
                <span class="nav-label">Uitloggen</span>
            </button>
        </form>
    <?php else: ?>
        <a href='login.php' class='login-button'>
            <span class="nav-label">Inloggen</span>
        </a>
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
                       <a href='product_detail.php?id=<?= $product['id'] ?>' class='add-to-cart-button'>
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
    
    <script src="https://www.chatbase.co/embed.min.js" id="chatbase-script" defer></script>

</body>
</html>
