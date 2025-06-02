<?php
include 'connect.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];

    $sql = "SELECT id, wachtwoord, is_admin FROM gebruikers WHERE gebruikersnaam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $gebruikersnaam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($wachtwoord, $user['wachtwoord'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $message = "<div class='message success'>Inloggen succesvol! <a href='index.php'>Ga verder</a></div>";
        } else {
            $message = "<div class='message error'>Ongeldig wachtwoord.</div>";
        }
    } else {
        $message = "<div class='message error'>Geen gebruiker gevonden met deze gebruikersnaam.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inloggen</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<style>
 /* Algemene stijl */
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f1f1f1;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Formulier container */
.form-container {
    background-color: #ffffff; 
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 450px;
    text-align: center;
}

/* Koptekst */
.form-container h1 {
    margin-bottom: 20px;
    color: #5d5d5d;
    font-size: 32px;
    font-weight: 600;
}

/* Labels en inputvelden */
label {
    display: block;
    text-align: left;
    margin-bottom: 5px;
    font-weight: 600;
    color: #888;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

input:focus {
    border-color: #d0a3f5;
    outline: none;
}

/* Knoppen */
button {
    background-color:rgb(62, 61, 62);
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color:rgb(132, 131, 133); 
}

/* Links onder het formulier */
.continue-shopping, .back-button {
    display: inline-block;
    margin-top: 20px;
    color:rgb(11, 11, 11);
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.continue-shopping:hover, .back-button:hover {
    color:rgb(220, 217, 217); 
}

/* Berichtweergave */
.message {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
}

.message.success {
    background-color: #e8f5e9; 
    color: #388e3c; 
}

.message.error {
    background-color: #ffebee; 
    color: #d32f2f; 
}
</style>
<body>
    <div class="form-container">
        <h1>Inloggen</h1>
        <?php echo $message; ?>
        <form method="POST" action="login.php">
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>
            <label for="wachtwoord">Wachtwoord:</label>
            <input type="password" id="wachtwoord" name="wachtwoord" required>
            <button type="submit">Inloggen</button>
        </form>
        <a href="register.php" class="continue-shopping">Account aanmaken</a>
        <a href="index.php" class="continue-shopping">Startpagina</a>
    </div>
</body>
</html>
