<?php
include 'connect.php';
session_start();

// Gebruiker moet ingelogd zijn
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$totaal_prijs = $_SESSION['total_price'] ?? 0;

// Controleer of winkelwagen gevuld is
if (empty($_SESSION['winkelwagen'])) {
    header("Location: winkelwagen.php");
    exit;
}

// PayPal instellingen
$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
$business_email = "carrychic@business.example.com";
$currency = "EUR";

// Bouw veilige PayPal-query
$params = [
    'cmd' => '_xclick',
    'business' => $business_email,
    'amount' => number_format($totaal_prijs, 2, '.', ''),
    'currency_code' => $currency,
    'return' => 'https://carrychic.itbusleyden.be/paypal_succes.php?tx=1', 
    'cancel_return' => 'https://carrychic.itbusleyden.be/betaling_geannuleerd.php'
];

// Doorverwijzen naar PayPal
$redirect_url = $paypal_url . '?' . http_build_query($params);
header("Location: $redirect_url");
exit;
?>
