<?php
// Verbinding maken via connect.php
include('connect.php');

// Verwerken van formulier voor het toevoegen van een verzendmethode
if (isset($_POST['add_method'])) {
    $method_name = $_POST['method_name'];
    $base_cost = $_POST['base_cost'];
    $cost_per_kg = $_POST['cost_per_kg'];
    $delivery_time = $_POST['delivery_time'];

    // SQL-query om nieuwe verzendmethode toe te voegen
    $query = "INSERT INTO shipping_methods (method_name, base_cost, cost_per_kg, delivery_time)
              VALUES ('$method_name', '$base_cost', '$cost_per_kg', '$delivery_time')";

    if ($conn->query($query) === TRUE) {
        $success_message = "Verzendmethode succesvol toegevoegd!";
    } else {
        $error_message = "Fout bij toevoegen van verzendmethode: " . $conn->error;
    }
}

// Verwerken van verwijderactie
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // SQL-query om verzendmethode te verwijderen
    $delete_query = "DELETE FROM shipping_methods WHERE id = $delete_id";

    if ($conn->query($delete_query) === TRUE) {
        $success_message = "Verzendmethode succesvol verwijderd!";
    } else {
        $error_message = "Fout bij verwijderen van verzendmethode: " . $conn->error;
    }
}

// Haal verzendmethoden op
$query = "SELECT * FROM shipping_methods";
$result = $conn->query($query);

// Sluit de verbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Verzending</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f9; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table th { background-color: #007bff; color: #fff; }
        .form-container { margin-top: 20px; }
        .form-container input, .form-container button { display: block; width: 100%; padding: 10px; margin: 10px 0; }
        .form-container button { background-color: #007bff; color: white; border: none; cursor: pointer; }
        .form-container button:hover { background-color: #0056b3; }
        .alert {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            display: none;
            position: relative;
        }
        .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert button {
            background: none;
            border: none;
            font-size: 16px;
            font-weight: bold;
            position: absolute;
            top: 5px;
            right: 10px;
            cursor: pointer;
            color: inherit;
        }
        .back-button { text-align: center; margin-top: 20px; }
        .back-button a {
            text-decoration: none;
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }
        .back-button a:hover { background-color: #5a6268; }
    </style>
    <script>
        // Dynamische meldingen tonen en automatisch verbergen
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.display = 'block';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000); // Verdwijnt na 5 seconden
            });
        });
    </script>
</head>
<body>
<div class="container">
    <h1>Beheer Verzending</h1>

    <!-- Dynamische succes- of foutmeldingen -->
    <?php if (!empty($success_message)): ?>
        <div class="alert success">
            <?php echo $success_message; ?>
            <button onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    <?php elseif (!empty($error_message)): ?>
        <div class="alert error">
            <?php echo $error_message; ?>
            <button onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    <?php endif; ?>

    <h2>Voeg een nieuwe verzendmethode toe</h2>
    <form action="manage_shipping_methods.php" method="POST" class="form-container">
        <input type="text" name="method_name" placeholder="Naam van verzendmethode" required>
        <input type="number" step="0.01" name="base_cost" placeholder="Basiskosten (€)" required>
        <input type="number" step="0.01" name="cost_per_kg" placeholder="Kosten per kg (€)" required>
        <input type="text" name="delivery_time" placeholder="Levertijd (bijv. 1-2 dagen)" required>
        <button type="submit" name="add_method">Toevoegen</button>
    </form>

    <h2>Bestaande verzendmethoden</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Basiskosten</th>
                <th>Kosten per kg</th>
                <th>Levertijd</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($result) && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['method_name']); ?></td>
                    <td>€<?php echo number_format($row['base_cost'], 2); ?></td>
                    <td>€<?php echo number_format($row['cost_per_kg'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['delivery_time']); ?></td>
                    <td>
                        <a href="edit_shipping_method.php?id=<?php echo $row['id']; ?>">Bewerken</a> |
                        <a href="manage_shipping_methods.php?delete_id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Weet je zeker dat je deze methode wilt verwijderen?');">Verwijderen</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">Geen verzendmethoden gevonden.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Terug naar Adminpaneel knop -->
    <div class="back-button">
        <a href="adminpanel.php">Terug naar Adminpaneel</a>
    </div>
</div>
</body>
</html>
