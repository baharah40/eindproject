<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM shipping_methods";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching shipping methods: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_shipping'])) {
    if (isset($_POST['shipping_method'])) {
        $_SESSION['shipping_method'] = $_POST['shipping_method'];
        header("Location: payment.php"); // Link naar je betaalpagina
        exit();
    } else {
        echo "Please select a shipping method.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Shipping Option</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        .option {
            margin: 15px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #f8f8f8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
        }
        .option:hover {
            background-color: #eaf5ff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        }
        .option input {
            margin-right: 15px;
            accent-color: #007bff;
        }
        .option label {
            display: flex;
            justify-content: space-between;
            width: 100%;
            cursor: pointer;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
        }
        .back-button {
            background-color: #6c757d;
            margin-top: 10px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Select Shipping Option</h1>
        <form action="shipping_options.php" method="POST">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <label class="option">
                        <input type="radio" name="shipping_method" value="<?php echo $row['id']; ?>" required>
                        <span><?php echo $row['method_name'] . " - €" . number_format($row['base_cost'], 2) . " (" . $row['delivery_time'] . ")"; ?></span>
                    </label>
                <?php endwhile; ?>
                <button type="submit" name="pay" formaction="payement.php">Pay</button>
                            <?php else: ?>
                <p>No shipping options available at the moment. Please try again later.</p>
            <?php endif; ?>
        </form>

        <!-- Back to Cart button -->
        <form action="cart.php" method="get">
            <button type="submit" class="back-button">Back to Cart</button>
        </form>
    </div>
</body>
</html>
