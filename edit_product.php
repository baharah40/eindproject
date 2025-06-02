<?php
include 'connect.php';

$product = null;
$message = '';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $sql = "SELECT naam, prijs, afbeelding, beschrijving FROM producten WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $message = "Product niet gevonden.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = $conn->real_escape_string($_POST['naam']);
    $prijs = floatval($_POST['prijs']);
    $beschrijving = $conn->real_escape_string($_POST['beschrijving']);
    
    // Check if a new image is uploaded
    if (!empty($_FILES['afbeelding']['name'])) {
        $afbeelding = basename($_FILES['afbeelding']['name']);
        move_uploaded_file($_FILES['afbeelding']['tmp_name'], "images/$afbeelding");

        $sql = "UPDATE producten SET naam='$naam', prijs=$prijs, beschrijving='$beschrijving', afbeelding='$afbeelding' WHERE id=$id";
    } else {
        $sql = "UPDATE producten SET naam='$naam', prijs=$prijs, beschrijving='$beschrijving' WHERE id=$id";
    }

    if ($conn->query($sql)) {
        $message = "Product succesvol bijgewerkt.";
        // Opnieuw ophalen voor nieuwe waarden
        $result = $conn->query("SELECT naam, prijs, afbeelding, beschrijving FROM producten WHERE id = $id");
        $product = $result->fetch_assoc();
    } else {
        $message = "Fout bij bijwerken: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Bewerken</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
</head>
<body>
    <div class="container">
        <h1>Product Bewerken</h1>
        
        <?php if (!empty($message)) echo "<p>$message</p>"; ?>

        <?php if ($product): ?>
            <form method="post" enctype="multipart/form-data">
                <label>Naam:</label><br>
                <input type="text" name="naam" value="<?php echo htmlspecialchars($product['naam']); ?>" required><br><br>

                <label>Prijs (€):</label><br>
                <input type="number" step="0.01" name="prijs" value="<?php echo htmlspecialchars($product['prijs']); ?>" required><br><br>

                <label>Beschrijving:</label><br>
                <textarea name="beschrijving" required><?php echo htmlspecialchars($product['beschrijving']); ?></textarea><br><br>

                <label>Afbeelding:</label><br>
                <input type="file" name="afbeelding"><br>
                <small>Huidige afbeelding: <?php echo htmlspecialchars($product['afbeelding']); ?></small><br><br>

                <input type="submit" value="Opslaan">
            </form>
        <?php else: ?>
            <p>Product niet gevonden.</p>
        <?php endif; ?>

        <br><a href="admin_dashboard.php">Terug naar Dashboard</a>
    </div>
</body>
</html>
