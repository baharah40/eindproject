<?php
include 'connect.php';
session_start();

// Controleer of gebruiker ingelogd is en adminrechten heeft
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$section = $_GET['section'] ?? 'products';
$message = "";

// === Verwijder afbeelding ===
if (isset($_POST['delete_image'])) {
    $image_id = intval($_POST['image_id']);
    $sql = "SELECT afbeelding FROM product_afbeeldingen WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        unlink('images/' . $row['afbeelding']);
        $delete = $conn->prepare("DELETE FROM product_afbeeldingen WHERE id = ?");
        $delete->bind_param("i", $image_id);
        $delete->execute();
        $message = "<div class='message success'>Afbeelding verwijderd.</div>";
    }
    $stmt->close();
}

// === Afbeelding vervangen ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['replace_image']) && isset($_FILES['new_image'])) {
    $image_id = intval($_POST['replace_image_id']);
    $new_image = $_FILES['new_image'];

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    if (in_array($new_image['type'], $allowed) && $new_image['size'] <= 5 * 1024 * 1024) {
        $stmt = $conn->prepare("SELECT afbeelding FROM product_afbeeldingen WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $old_path = 'images/' . $row['afbeelding'];
            if (file_exists($old_path)) {
                unlink($old_path);
            }

            $new_name = uniqid('product_', true) . '.' . pathinfo($new_image['name'], PATHINFO_EXTENSION);
            move_uploaded_file($new_image['tmp_name'], 'images/' . $new_name);

            $update = $conn->prepare("UPDATE product_afbeeldingen SET afbeelding = ? WHERE id = ?");
            $update->bind_param("si", $new_name, $image_id);
            $update->execute();
            $update->close();

            $message .= "<div class='message success'>Afbeelding vervangen.</div>";
        }

        $stmt->close();
    } else {
        $message .= "<div class='message error'>Ongeldig bestandstype of bestand te groot.</div>";
    }
}

// === Verwijder product ===
if (isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);
    
    // Verwijder eerst de afbeeldingen van dit product
    $stmt = $conn->prepare("SELECT afbeelding FROM product_afbeeldingen WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        unlink('images/' . $row['afbeelding']); // Verwijder afbeelding van server
    }
    $stmt->close();

    // Verwijder dan het product zelf
    $stmt = $conn->prepare("DELETE FROM producten WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    $message = "<div class='message success'>Product succesvol verwijderd.</div>";
}

// === Product Bewerken ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $product_id = intval($_POST['product_id'] ?? 0);
    $naam = htmlspecialchars($_POST['naam']);
    $prijs = htmlspecialchars($_POST['prijs']);
    $beschrijving = htmlspecialchars($_POST['beschrijving']);

    $sql = "UPDATE producten SET naam = ?, prijs = ?, beschrijving = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $naam, $prijs, $beschrijving, $product_id);
    $stmt->execute();
    $stmt->close();

    // Nieuwe afbeeldingen uploaden
    if (!empty($_FILES['afbeeldingen']['name'][0])) {
        foreach ($_FILES['afbeeldingen']['name'] as $index => $name) {
            $tmp_name = $_FILES['afbeeldingen']['tmp_name'][$index];
            $type = $_FILES['afbeeldingen']['type'][$index];
            $size = $_FILES['afbeeldingen']['size'][$index];
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

            if (in_array($type, $allowed) && $size <= 5 * 1024 * 1024) {
                $new_name = uniqid('product_', true) . '.' . pathinfo($name, PATHINFO_EXTENSION);
                move_uploaded_file($tmp_name, 'images/' . $new_name);
                $insert = $conn->prepare("INSERT INTO product_afbeeldingen (product_id, afbeelding) VALUES (?, ?)");
                $insert->bind_param("is", $product_id, $new_name);
                $insert->execute();
                $insert->close();
            }
        }
        $message .= "<div class='message success'>Afbeeldingen geüpload.</div>";
    }
}

// === Producten ophalen ===
$sql = "SELECT * FROM producten ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
       body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
    margin: 0;
    padding: 0;
}

h1, h2 {
    color: #2C3E50;
}

.container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Hamburger menu */
.hamburger-menu {
    position: fixed;
    top: 0;
    left: 0;
    background-color: #2C3E50;
    color: white;
    height: 100%;
    width: 250px;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 1000;
    padding: 20px;
    box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.5);
}

.hamburger-menu.open {
    transform: translateX(0);
}

.hamburger-menu ul {
    list-style-type: none;
    padding: 0;
}

.hamburger-menu ul li {
    margin: 15px 0;
}

.hamburger-menu ul li a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    transition: color 0.3s ease;
}

.hamburger-menu ul li a:hover {
    color: #1ABC9C;
}

.hamburger-button {
    position: fixed;
    top: 20px;
    left: 20px;
    background-color: #2C3E50;
    color: white;
    padding: 10px;
    border: none;
    cursor: pointer;
    z-index: 1100;
}

/* Productenlijst */
.product-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: flex-start;
}

.product-item {
    width: 300px;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
    margin-bottom: 30px;
}

.product-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

/* Grote afbeelding */
.large-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* Kleine afbeeldingen */
.product-images {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 10px;
}

.product-images .small-images {
    position: relative;
    width: 200px;
}

.product-images img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    display: block;
}

/* Delete button */
.delete-button {
    background-color: #e74c3c;
    color: white;
    padding: 5px;
    font-weight: bold;
    border-radius: 50%;
    cursor: pointer;
    position: absolute;
    top: -5px;
    right: -5px;
    border: none;
}

.delete-button[title="Vervangen"] {
    background-color: #3498db;
    bottom: -5px;
    top: auto;
    right: -5px;
}

.delete-button:hover {
    opacity: 0.9;
}

/* Product verwijderen */
.delete-product-button {
    background-color: #e74c3c;
    color: white;
    padding: 10px 15px;
    font-size: 14px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
    transition: background-color 0.3s ease;
}

.delete-product-button:hover {
    background-color: #c0392b;
}

/* Bewerken formulier */
.edit-form input,
.edit-form textarea,
.edit-form button {
    width: 90%;
    padding: 12px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

.edit-form textarea {
    resize: vertical;
    min-height: 150px;
}

.edit-form button {
    background-color: #3498db;
    color: white;
    cursor: pointer;
    font-weight: bold;
}

.edit-form button:hover {
    background-color: #2980b9;
}

/* Berichten */
.message {
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 4px;
    color: white;
}

.message.success {
    background-color: #28a745;
}

.message.error {
    background-color: #dc3545;
}

    </style>
    <script>
        // Toggle de zichtbaarheid van het hamburger-menu
        function toggleMenu() {
            var menu = document.getElementById('hamburger-menu');
            menu.classList.toggle('open'); // Hiermee open of sluit je het menu
        }

        // Sluit het menu wanneer je op de sluitknop drukt
        function closeMenu() {
            var menu = document.getElementById('hamburger-menu');
            menu.classList.remove('open'); // Hiermee sluit je het menu
        }

        // Functie voor bevestigen van de verwijderactie
        function confirmDelete() {
            return confirm("Weet je zeker dat je dit product wilt verwijderen? Dit kan niet ongedaan worden gemaakt.");
        }
    </script>
</head>
<body>
<button class="hamburger-button" onclick="toggleMenu()">☰ Menu</button>
<div class="hamburger-menu" id="hamburger-menu">
    <button onclick="closeMenu()">✕</button>
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="?section=products">Producten beheren</a></li>
        <li><a href="add_product.php">Nieuw Product Toevoegen</a></li>
        <li><a href="socialMedia-link.php">Sociale media links</a></li>
        <li><a href="index.php">Terug naar Webshop</a></li>
    </ul>
</div>
<div class="container">
    <h1>Producten Beheren</h1>
    <?php echo $message; ?>
    <div class="product-list">
        <?php while ($row = $result->fetch_assoc()):
            $product_id = $row['id'];
            $stmt = $conn->prepare("SELECT id, afbeelding FROM product_afbeeldingen WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $afbeeldingen_result = $stmt->get_result();
            $first_image = true;
            ?>
        <div class="product-item">
            <h2><?= htmlspecialchars($row['naam']) ?></h2>
            <div class="product-images">
    <?php 
    $stmt->data_seek(0); // Reset resultaat pointer
    while ($img = $afbeeldingen_result->fetch_assoc()):
        ?>
        <div class="small-images" style="position: relative;">
            <img src="images/<?= htmlspecialchars($img['afbeelding']) ?>" class="large-image" alt="Productafbeelding">
            <!-- Verwijder afbeelding -->
            <form method="post" style="display: inline;" onsubmit="return confirmDelete()">
                <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                <button type="submit" name="delete_image" class="delete-button" title="Verwijder">✖</button>
            </form>

            <!-- Vervang afbeelding -->
            <form method="post" enctype="multipart/form-data" style="margin-top: 5px;" onsubmit="return confirm('Weet je zeker dat je deze afbeelding wilt vervangen?')">
                <input type="hidden" name="replace_image_id" value="<?= $img['id'] ?>">
                <input type="file" name="new_image" accept="image/*" required>
                <button type="submit" name="replace_image" class="delete-button" title="Vervangen" style="top: auto; bottom: -5px;">⟳</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

            <form action="" method="post" enctype="multipart/form-data" class="edit-form">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <input type="text" name="naam" value="<?= htmlspecialchars($row['naam']) ?>" required>
                <input type="number" step="0.01" name="prijs" value="<?= $row['prijs'] ?>" required>
                <textarea name="beschrijving" rows="4" required><?= htmlspecialchars($row['beschrijving']) ?></textarea>
                <button type="submit" name="edit_product">Bewerk Product</button>
            </form>
            
            <!-- Verwijder Product Knop -->
            <form action="" method="post" style="margin-top: 10px;" onsubmit="return confirmDelete()">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <button type="submit" name="delete_product" class="delete-product-button">Verwijder Product</button>
                </form>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
