<?php
session_start();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Betaling geannuleerd</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff3f3;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px 40px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 450px;
            text-align: center;
        }
        h2 {
            color: #d9534f;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.1em;
            margin-bottom: 25px;
            color: #333;
        }
        a {
            display: inline-block;
            background-color: #d9534f;
            color: white;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Betaling Geannuleerd</h2>
        <p>Je betaling is geannuleerd of mislukt. Er is niets in rekening gebracht.</p>
        <a href="winkelwagen.php">Terug naar winkelwagen</a>
    </div>
</body>
</html>
