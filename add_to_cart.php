<?php
session_start();
include 'connect.php';

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Controleer of de winkelwagen al bestaat in de sessie
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Voeg het product toe aan de winkelwagen
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += 1; // Verhoog de hoeveelheid als het product al in de winkelwagen zit
    } else {
        $_SESSION['cart'][$product_id] = 1; // Voeg het product toe aan de winkelwagen
    }

    echo "success"; // Geef een succesbericht terug
} else {
    echo "error"; // Geef een foutmelding terug als er geen product_id is
}
?>