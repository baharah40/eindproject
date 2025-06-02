<?php
include 'connect.php';
session_start();
// Verwerk het formulier voor het toevoegen van een product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $prijs = $_POST['prijs'];
    $afbeeldingen = $_FILES['afbeelding']; 

    // Beveiliging tegen SQL-injectie en XSS
    $naam = htmlspecialchars($naam);
    $prijs = htmlspecialchars($prijs);

    // Controleer of er afbeeldingen zijn geüpload
    if (count($afbeeldingen['name']) > 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $success = true;

        // Voeg het product eerst toe aan de database om het product_id te krijgen
        $sql = "INSERT INTO producten (naam, prijs) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $naam, $prijs);

        if ($stmt->execute()) {
            $product_id = $stmt->insert_id;

            // Loop door alle geüploade afbeeldingen
            foreach ($afbeeldingen['name'] as $key => $afbeelding) {
                $file_type = $afbeeldingen['type'][$key];
                $file_size = $afbeeldingen['size'][$key];

                // Controleer bestandstype
                if (!in_array($file_type, $allowed_types)) {
                    $success = false;
                    $message = "<div class='message error'>Ongeldig bestandstype. Alleen .jpg, .png, .gif zijn toegestaan.</div>";
                    break;
                }

                // Controleer bestandsgrootte
                if ($file_size > $max_size) {
                    $success = false;
                    $message = "<div class='message error'>Bestand is te groot. Maximaal 5MB per bestand.</div>";
                    break;
                }

                // Genereer een unieke bestandsnaam
                $file_extension = pathinfo($afbeelding, PATHINFO_EXTENSION);
                $new_filename = uniqid('product_', true) . '.' . $file_extension; // Unieke naam
                $target_path = "images/" . $new_filename;

                // Verplaats het geüploade bestand naar de images map
                if (move_uploaded_file($afbeeldingen['tmp_name'][$key], $target_path)) {
                    // Voeg de afbeelding toe aan de database
                    $sql = "INSERT INTO product_afbeeldingen (product_id, afbeelding) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $product_id, $new_filename);

                    if (!$stmt->execute()) {
                        $success = false;
                        $message = "<div class='message error'>Fout bij het toevoegen van een afbeelding: " . $stmt->error . "</div>";
                        break;
                    }
                } else {
                    $success = false;
                    $message = "<div class='message error'>Fout bij het uploaden van een afbeelding.</div>";
                    break;
                }
            }

            if ($success) {
                $message = "<div class='message success'>Product succesvol toegevoegd met meerdere afbeeldingen!</div>";
            }

            $stmt->close();
        } else {
            $message = "<div class='message error'>Fout bij het toevoegen van het product: " . $stmt->error . "</div>";
        }
    } else {
        $message = "<div class='message error'>Geen afbeeldingen geüpload.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Toevoegen</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
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

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="file"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .buttons a {
            text-decoration: none;
            padding: 10px 15px;
            background-color: #6c757d;
            color: #fff;
            border-radius: 5px;
            font-size: 16px;
        }

        .buttons a:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nieuw Product Toevoegen</h1>
        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="naam">Naam:</label>
            <input type="text" id="naam" name="naam" required>

            <label for="prijs">Prijs:</label>
            <input type="text" id="prijs" name="prijs" required>

            <label for="afbeelding">Afbeeldingen:</label>
<input type="file" id="afbeelding" name="afbeelding[]" accept="image/jpeg, image/png, image/gif" multiple required>


            <button type="submit">Product Toevoegen</button>
        </form>

        <div class="buttons">
        <a href="index.php">Terug naar producten</a>
        <a href="admin.php">Terug naar Admin Panel</a>
        </div>
    </div>
</body>
</html>