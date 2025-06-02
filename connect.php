<?php
/*
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webshop";

// Maak verbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
*/
?>

<?php


//echo "connect.php geladen<br>";

$servername = "localhost";
$username = "admin@carrychic."; 
$password = "Queen2006";
$dbname = "carrychic";

// Maak verbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
    
?>

