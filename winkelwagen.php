<?php
include 'connect.php';
session_start();

function maakKey($id, $afbeelding) {
    return $id . '|' . $afbeelding;
}

// Voeg product toe aan winkelwagen
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $afbeelding = isset($_GET['afbeelding']) ? $_GET['afbeelding'] : 'placeholder.png';

    if (!isset($_SESSION['winkelwagen'])) {
        $_SESSION['winkelwagen'] = array();
    }

    $key = maakKey($id, $afbeelding);

    if (isset($_SESSION['winkelwagen'][$key])) {
        $_SESSION['winkelwagen'][$key]['aantal']++;
    } else {
        $_SESSION['winkelwagen'][$key] = [
            'id' => $id,
            'aantal' => 1,
            'afbeelding' => $afbeelding
        ];
    }

    // Redirect om dubbele toevoeging bij refresh te voorkomen
    $url = strtok($_SERVER["REQUEST_URI"], '?'); 
    header("Location: $url");
    exit;
}

// Verwijder product uit winkelwagen
if (isset($_GET['remove'])) {
    $remove_key = $_GET['remove'];
    if (isset($_SESSION['winkelwagen'][$remove_key])) {
        if ($_SESSION['winkelwagen'][$remove_key]['aantal'] > 1) {
            $_SESSION['winkelwagen'][$remove_key]['aantal']--;
        } else {
            unset($_SESSION['winkelwagen'][$remove_key]);
        }
    }
}

// Haal producten op uit winkelwagen
$producten_in_winkelwagen = [];
$totaal_prijs = 0;

if (!empty($_SESSION['winkelwagen'])) {
    foreach ($_SESSION['winkelwagen'] as $key => $data) {
        $product_id = $data['id'];
        $aantal = $data['aantal'];
        $afbeelding = $data['afbeelding'];

        $sql = "SELECT id, naam, prijs FROM producten WHERE id = $product_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $product['aantal'] = $aantal;
            $product['afbeelding'] = $afbeelding;
            $product['key'] = $key; 

            $producten_in_winkelwagen[] = $product;
            $totaal_prijs += $product['prijs'] * $aantal;
        }
    }
    $_SESSION['total_price'] = $totaal_prijs;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Winkelwagen</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
    </style>
</head>
<body>
<div class="container">
    <h1>Winkelwagen</h1>
    <ul class="product-list">
        <?php if (!empty($producten_in_winkelwagen)): ?>
            <?php foreach ($producten_in_winkelwagen as $product): ?>
                <li class="product-item">
                    <img src="images/<?php echo htmlspecialchars($product['afbeelding']); ?>" alt="<?php echo htmlspecialchars($product['naam']); ?>">
                    <div class="product-info">
                        <h2><?php echo htmlspecialchars($product['naam']); ?></h2>
                        <p>€<?php echo number_format($product['prijs'], 2); ?></p>
                        <span class="product-quantity">Aantal: x<?php echo $product['aantal']; ?></span>
                    </div>
                    <a href="winkelwagen.php?remove=<?php echo urlencode($product['key']); ?>" class="remove-button">Verwijderen</a>
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
    <a href="afrekenen.php" class="continue-shopping">Afrekenen</a>
</div>
</body>
</html>