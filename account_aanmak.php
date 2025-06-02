<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Gebruiker ophalen
$sql = "SELECT gebruikersnaam, email, profiel_foto FROM gebruikers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($gebruikersnaamHuidig, $emailHuidig, $profielFotoHuidig);
$stmt->fetch();
$stmt->close();

$errors = [];
$success = false;

// Bestellingen ophalen, inclusief productfoto
$sql_bestellingen = "
    SELECT product_naam, prijs, aantal, bestel_datum, product_foto
    FROM bestellingen
    WHERE gebruiker_id = ?
    ORDER BY bestel_datum DESC
";

$stmt_bestellingen = $conn->prepare($sql_bestellingen);
$stmt_bestellingen->bind_param("i", $user_id);
$stmt_bestellingen->execute();
$result_bestellingen = $stmt_bestellingen->get_result();

$bestellingen_per_datum = [];

while ($row = $result_bestellingen->fetch_assoc()) {
$datum = date('d-m-Y H:i', strtotime($row['bestel_datum']));
    if (!isset($bestellingen_per_datum[$datum])) {
        $bestellingen_per_datum[$datum] = [];
    }
    $bestellingen_per_datum[$datum][] = $row;
}
$stmt_bestellingen->close();

// --- PROFIELFOTO VERWIJDEREN ---
if (isset($_POST['remove_photo'])) {
    $uploadDir = 'profile_images/';
    
    if ($profielFotoHuidig && file_exists($uploadDir . $profielFotoHuidig)) {
        unlink($uploadDir . $profielFotoHuidig);
    }

    $sql_remove_photo = "UPDATE gebruikers SET profiel_foto = NULL WHERE id = ?";
    $stmt_remove = $conn->prepare($sql_remove_photo);
    $stmt_remove->bind_param("i", $user_id);
    $stmt_remove->execute();
    $stmt_remove->close();

    $profielFotoHuidig = null;
    $success = true;
}

// --- ACCOUNT BIJWERKEN ---
if (isset($_POST['submit'])) {
    $gebruikersnaam = trim($_POST['gebruikersnaam']);
    $email = trim($_POST['email']);
    $wachtwoord = $_POST['wachtwoord']; 
    $profileFoto = $_FILES['profile_foto'];

    $fotoNaam = $profielFotoHuidig;

    // Check of gebruikersnaam of email al bestaat bij een andere gebruiker
    $sql_check = "SELECT id FROM gebruikers WHERE (gebruikersnaam = ? OR email = ?) AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ssi", $gebruikersnaam, $email, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $errors[] = "Gebruikersnaam of e-mail bestaat al bij een andere gebruiker.";
    }
    $stmt_check->close();

    // Profielfoto uploaden
    $uploadDir = 'profile_images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (isset($profileFoto) && $profileFoto['error'] === 0) {
        $ext = strtolower(pathinfo($profileFoto['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $nieuwFotoNaam = uniqid() . '.' . $ext;
            $uploadPad = $uploadDir . $nieuwFotoNaam;

            if (move_uploaded_file($profileFoto['tmp_name'], $uploadPad)) {
                if ($profielFotoHuidig && file_exists($uploadDir . $profielFotoHuidig)) {
                    unlink($uploadDir . $profielFotoHuidig);
                }
                $fotoNaam = $nieuwFotoNaam;
            } else {
                $errors[] = "Fout bij uploaden van profielfoto.";
            }
        } else {
            $errors[] = "Ongeldig bestandstype voor profielfoto.";
        }
    }

    if (empty($errors)) {
        if (!empty($wachtwoord)) {
            $wachtwoordHash = password_hash($wachtwoord, PASSWORD_BCRYPT);
            $sql_update = "UPDATE gebruikers SET gebruikersnaam = ?, email = ?, wachtwoord = ?, profiel_foto = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssssi", $gebruikersnaam, $email, $wachtwoordHash, $fotoNaam, $user_id);
        } else {
            $sql_update = "UPDATE gebruikers SET gebruikersnaam = ?, email = ?, profiel_foto = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssi", $gebruikersnaam, $email, $fotoNaam, $user_id);
        }

        if ($stmt_update->execute()) {
            $success = true;
            $gebruikersnaamHuidig = $gebruikersnaam;
            $emailHuidig = $email;
            $profielFotoHuidig = $fotoNaam;
        } else {
            $errors[] = "Fout bij bijwerken van account: " . $stmt_update->error;
        }
        $stmt_update->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Aanpassen</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .button-container {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            align-items: center;
        }

        .button-container a, .button-container button {
            text-decoration: none;
            color: #333;
            border: 1px solid #ccc;
            padding: 6px 12px;
            border-radius: 4px;
            background-color: #f0f0f0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button-container a:hover, .button-container button:hover {
            background-color: #ddd;
        }
       .bestellingen-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    justify-content: flex-start;
}

.bestelling-card {
    border: 1px solid #ccc;
    padding: 15px;
    border-radius: 8px;
    width: 220px;
    box-sizing: border-box;
    background-color: #f9f9f9;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

        .container {
            max-width: 1300px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="file"] {
            padding: 0;
        }
        button[type="submit"] {
            background-color:rgb(12, 24, 58);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        

    </style>
</head>
<body>
<div class="container">
    <h2>Pas je account aan</h2>

    <?php if ($success): ?>
        <p style="color:green;">Je account is succesvol bijgewerkt.</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Gebruikersnaam:</label>
        <input type="text" name="gebruikersnaam" value="<?= htmlspecialchars($gebruikersnaamHuidig) ?>" required><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($emailHuidig) ?>" required><br>

        <label>Nieuw wachtwoord (leeg laten als je niet wilt veranderen):</label>
        <input type="password" name="wachtwoord"><br>

        <label>Profielfoto:</label><br>

        <?php if ($profielFotoHuidig): ?>
            <img src="profile_images/<?= htmlspecialchars($profielFotoHuidig) ?>" alt="Profielfoto" style="max-width:150px; max-height:150px;"><br>
            
            <!-- Verwijder profielfoto knop -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="remove_photo" value="1">
                <button type="submit">Verwijder Profielfoto</button>
            </form>

        <?php endif; ?>

        <input type="file" name="profile_foto" accept="image/*"><br><br>

        <div class="button-container">
            <a href="index.php">← Terug naar Home</a>
            <button type="submit" name="submit">Account bijwerken</button>
        </div>
    </form>

    <hr>
  <?php if (!empty($bestellingen_per_datum)): ?>
    <h3>Jouw Bestellingen</h3>
    <?php foreach ($bestellingen_per_datum as $datum => $bestellingen): ?>
        <h4>Besteld op <?= htmlspecialchars($datum) ?></h4>
        <div class="bestellingen-container">
            <?php foreach ($bestellingen as $bestelling): ?>
                <div class="bestelling-card">
<?php
    $productFoto = !empty($bestelling['product_foto']) && file_exists("images/" . $bestelling['product_foto'])
        ? htmlspecialchars($bestelling['product_foto'])
        : 'placeholder.png';
?>
<img src="images/<?= $productFoto ?>" alt="<?= htmlspecialchars($bestelling['product_naam']) ?>" style="max-width:100%; height:auto;"><br>
                    <h4><?= htmlspecialchars($bestelling['product_naam']) ?></h4>
                    <p>Aantal: <?= htmlspecialchars($bestelling['aantal']) ?></p>
                    <p>Prijs per stuk: €<?= number_format($bestelling['prijs'], 2, ',', '.') ?></p>
                    <p>Totaal: €<?= number_format($bestelling['prijs'] * $bestelling['aantal'], 2, ',', '.') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Je hebt nog geen bestellingen geplaatst.</p>
<?php endif; ?>

</div>
</body>
</html>
