<?php
include 'connect.php';

$currentUserId = 1; 

// Toggle naar Admin
if (isset($_GET['make_admin_id'])) {
    $user_id = (int)$_GET['make_admin_id'];
    
    $sql_update = "UPDATE gebruikers SET is_admin = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: gebruikers_beheer.php");
    exit;
}

// Toggle naar Gebruiker
if (isset($_GET['make_user_id'])) {
    $user_id = (int)$_GET['make_user_id'];
    
    $sql_update = "UPDATE gebruikers SET is_admin = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: gebruikers_beheer.php");
    exit;
}

// Verwijder gebruiker met bevestiging
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    if ($delete_id !== $currentUserId) {
        $stmt = $conn->prepare("DELETE FROM gebruikers WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: gebruikers_beheer.php");
    exit;
}

// Haal gebruikers op, inclusief registratie datum
$sql_users = "SELECT id, gebruikersnaam, email, is_admin, created_at FROM gebruikers ORDER BY gebruikersnaam ASC";
$result_users = $conn->query($sql_users);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gebruikersbeheer</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <script>
        function confirmDelete(username, id) {
            if (confirm('Weet je zeker dat je gebruiker "' + username + '" wilt verwijderen?')) {
                window.location.href = '?delete_id=' + id;
            }
            return false;
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Gebruikersbeheer</h1>

    <?php if ($result_users->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Gebruikersnaam</th>
                <th>Email</th>
                <th>Datum registratie</th>
                <th>Admin Status</th>
                <th>Acties</th>
            </tr>
            <?php while ($user = $result_users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['gebruikersnaam']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($user['created_at']))); ?></td>
                    <td><?php echo $user['is_admin'] ? 'Admin' : 'Gebruiker'; ?></td>
                    <td>
                        <?php if ($user['is_admin']): ?>
                            <a href="?make_user_id=<?php echo $user['id']; ?>">Maak Gebruiker</a>
                        <?php else: ?>
                            <a href="?make_admin_id=<?php echo $user['id']; ?>">Maak Admin</a>
                        <?php endif; ?>

                        <!-- Verwijder Gebruiker, maar niet zichzelf -->
                        <?php if ($user['id'] !== $currentUserId): ?>
                            | 
                            <a href="#" onclick="return confirmDelete('<?php echo addslashes(htmlspecialchars($user['gebruikersnaam'])); ?>', <?php echo $user['id']; ?>)">Verwijderen</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Er zijn geen gebruikers beschikbaar.</p>
    <?php endif; ?>

    <br>
    <a href="index.php">Terug naar producten</a>
    <br>
    <br>
    <a href="admin.php">Terug naar Dashboard</a>
</div>
</body>
</html>
