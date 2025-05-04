<?php
include 'connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $email = $_POST['email'];
    $wachtwoord = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT);

    // Check if username already exists
    $sql = "SELECT id FROM gebruikers WHERE gebruikersnaam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $gebruikersnaam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "<div class='message error'>Gebruikersnaam bestaat al.</div>";
    } else {
        $sql = "INSERT INTO gebruikers (gebruikersnaam, email, wachtwoord) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $gebruikersnaam, $email, $wachtwoord);

        if ($stmt->execute()) {
            $message = "<div class='message success'>Registratie succesvol! <a href='login.php'>Inloggen</a></div>";
        } else {
            $message = "<div class='message error'>Error: " . $stmt->error . "</div>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registreren</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<style>
    /* Algemene stijl */
    body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f1f1f1; /* Lichtgrijze achtergrond */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Formulier container */
.form-container {
    background-color: #ffffff; /* Witte achtergrond voor het formulier */
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
    color: #5d5d5d; /* Donkergrijze kleur voor de tekst */
    font-size: 32px;
    font-weight: 600;
}

/* Labels en inputvelden */
label {
    display: block;
    text-align: left;
    margin-bottom: 5px;
    font-weight: 600;
    color: #888; /* Lichtgrijze kleur voor labels */
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
    border-color: #d0a3f5; /* Lavendelkleurige focusrand */
    outline: none;
}

/* Knoppen */
button {
    background-color:rgb(62, 61, 62); /* Lavendelkleur voor de knop */
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
    background-color:rgb(132, 131, 133); /* Donkerder lavendel bij hover */
}

/* Links onder het formulier */
.continue-shopping, .back-button {
    display: inline-block;
    margin-top: 20px;
    color:rgb(11, 11, 11); /* Lavendelkleur voor links */
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.continue-shopping:hover, .back-button:hover {
    color:rgb(220, 217, 217); /* Donkerder lavendel bij hover */
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
    background-color: #e8f5e9; /* Zeer lichtgroene achtergrond voor succesberichten */
    color: #388e3c; /* Donkergroen voor succesbericht */
}

.message.error {
    background-color: #ffebee; /* Zeer lichtroze achtergrond voor foutberichten */
    color: #d32f2f; /* Donkerrood voor foutbericht */
}

</style>
<body>
    <div class="form-container">
        <h1>Registreren</h1>
        <?php echo $message; ?>
        <form method="POST" action="register.php">
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="wachtwoord">Wachtwoord:</label>
            <input type="password" id="wachtwoord" name="wachtwoord" required>
            <button type="submit">Registreren</button>
        </form>
        <a href="index.php" class="continue-shopping">Startpagina</a>
        <a href="login.php" class="back-button">Inloggen</a>
    </div>
</body>
</html>
