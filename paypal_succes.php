<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Je moet ingelogd zijn.");
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['tx'])) {
    die("Geen geldige PayPal-transactie ontvangen.");
}

if (empty($_SESSION['winkelwagen'])) {
    die("Je winkelwagen is leeg.");
}

foreach ($_SESSION['winkelwagen'] as $item) {
    $product_id = $item['id'];
    $aantal = $item['aantal'];
    $afbeelding = $item['afbeelding'];

    $stmt = $conn->prepare("SELECT naam, prijs FROM producten WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $naam = $row['naam'];
        $prijs = $row['prijs'];

        $stmt_insert = $conn->prepare("INSERT INTO bestellingen (gebruiker_id, product_naam, product_foto, prijs, aantal) VALUES (?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("issdi", $user_id, $naam, $afbeelding, $prijs, $aantal);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    $stmt->close();
}

// Leeg winkelwagen 
unset($_SESSION['winkelwagen']);
unset($_SESSION['total_price']);

// Toon bevestiging
$message_title = "Bedankt voor je bestelling!";
$message_body = "Je betaling is succesvol ontvangen.";
$link_href = "index.php";
$link_text = "Terug naar de webshop";
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Betaling geslaagd</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px 40px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 450px;
            text-align: center;
        }
        h2 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.1em;
            margin-bottom: 25px;
            color: #333;
        }
        a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><?= htmlspecialchars($message_title) ?></h2>
    <p><?= htmlspecialchars($message_body) ?></p>
    <a href="<?= htmlspecialchars($link_href) ?>"><?= htmlspecialchars($link_text) ?></a>
</div>
</body>
</html>
