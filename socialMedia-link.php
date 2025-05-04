<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $link = trim($_POST['link']);

        if (!empty($name) && !empty($link) && filter_var($link, FILTER_VALIDATE_URL)) {
            $sql = "UPDATE social_media_links SET name = ?, link = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $link, $id);
            $stmt->execute();
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM social_media_links WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif (isset($_POST['add'])) {
        $names = $_POST['name'];
        $links = $_POST['link'];

        if (is_array($names) && is_array($links) && count($names) == count($links)) {
            for ($i = 0; $i < count($names); $i++) {
                $name = trim($names[$i]);
                $link = trim($links[$i]);

                if (!empty($name) && !empty($link) && filter_var($link, FILTER_VALIDATE_URL)) {
                    $sql = "INSERT INTO social_media_links (name, link) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $name, $link);
                    $stmt->execute();
                }
            }
        }
    }
}

// Haal de bijgewerkte gegevens direct opnieuw op
$result = $conn->query("SELECT * FROM social_media_links");
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Social Media Koppelingen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="url"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .success-message {
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .error-message {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .back-to-admin {
            display: inline-block;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
        }
        .back-to-admin:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Beheer Social Media Koppelingen</h1>

        <form method="POST" action="">
            <h2>Nieuwe Koppelingen Toevoegen</h2>
            <div class="form-group">
                <label for="name[]">Naam sociaal mediakanaal:</label>
                <input type="text" name="name[]" required>
            </div>
            <div class="form-group">
                <label for="link[]">Link sociaal mediakanaal:</label>
                <input type="url" name="link[]" required>
            </div>
            <button type="submit" name="add">Opslaan</button>
        </form>

        <h2>Bestaande Koppelingen</h2>
        <table>
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Link</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="POST" action="">
                            <td>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                            </td>
                            <td>
                                <input type="url" name="link" value="<?php echo htmlspecialchars($row['link']); ?>">
                            </td>
                            <td class="action-buttons">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="update">Bijwerken</button>
                                <button type="submit" name="delete">Verwijderen</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <button onclick="location.href='admin.php'" class="back-to-admin">Terug naar Admin Panel</button>
    </div>
</body>
</html>
<?php $conn->close(); ?>
