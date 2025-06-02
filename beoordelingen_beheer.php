<?php
include 'connect.php';

// Verwijder beoordeling
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql_delete = "DELETE FROM product_reviews WHERE id = $delete_id";
    $conn->query($sql_delete);
    header("Location: beoordelingen_beheer.php");
    exit;
}

// Haal beoordelingen op
$sql_reviews = "SELECT r.id, r.naam, r.beoordeling, r.datum, p.naam AS product_naam 
                FROM product_reviews r 
                LEFT JOIN producten p ON r.product_id = p.id 
                ORDER BY r.datum DESC";
$result_reviews = $conn->query($sql_reviews);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Beoordelingen Beheer</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
</head>
<body>
<div class="container">
    <h1>Beoordelingen Beheer</h1>

    <?php if ($result_reviews->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Product</th>
                <th>Naam</th>
                <th>Beoordeling</th>
                <th>Datum</th>
                <th>Actie</th>
            </tr>
            <?php while ($review = $result_reviews->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['product_naam']); ?></td>
                    <td><?php echo htmlspecialchars($review['naam']); ?></td>
                    <td><?php echo htmlspecialchars($review['beoordeling']); ?></td>
                    <td><?php echo htmlspecialchars($review['datum']); ?></td>
                    <td>
                        <a href="?delete_id=<?php echo $review['id']; ?>" onclick="return confirm('Weet je zeker dat je deze beoordeling wilt verwijderen?');">Verwijderen</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Er zijn geen beoordelingen beschikbaar.</p>
    <?php endif; ?>

    <br>
    <a href="index.php">Terug naar producten</a>
    <br>
    <br>
    <a href="admin.php">Terug naar Dashboard</a>


</div>
</body>
</html>
