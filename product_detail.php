<?php
include 'connect.php';

$product = null;
$afbeeldingen = [];

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Haal productgegevens op
    $sql = "SELECT naam, prijs, beschrijving FROM producten WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }

    // Haal afbeeldingen op
    $sql_afbeeldingen = "SELECT afbeelding FROM product_afbeeldingen WHERE product_id = $id";
    $result_afbeeldingen = $conn->query($sql_afbeeldingen);
    while ($row = $result_afbeeldingen->fetch_assoc()) {
        $afbeeldingen[] = $row['afbeelding'];
    }

    // Beoordeling toevoegen
    if (isset($_POST['submit_review'])) {
        $naam = $conn->real_escape_string($_POST['naam']);
        $beoordeling = $conn->real_escape_string($_POST['beoordeling']);
        $datum = date('Y-m-d H:i:s');
        $sql_review = "INSERT INTO product_reviews (product_id, naam, beoordeling, datum) VALUES ($id, '$naam', '$beoordeling', '$datum')";
        $conn->query($sql_review);
        header("Location: ".$_SERVER['REQUEST_URI']); // pagina verversen
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Productdetails</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .product-detail {
            display: flex;
            gap: 40px;
            margin-top: 30px;
        }

        .product-detail img {
            width: 320px;
            height: auto;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .thumbnail-list {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .thumbnail-list img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 2px solid #ccc;
            cursor: pointer;
        }

        .thumbnail-list img:hover {
            border-color: #007bff;
        }

        .image-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .image-overlay img {
            max-width: 90%;
            max-height: 90%;
        }

        .close-button {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        .product-info h1 {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .review {
    background-color: #ffffff;
    border-left: 4px solid #555555;
    padding: 16px 20px;
    margin: 20px 0;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.review:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

form textarea, form input[type="text"] {
    width: 100%;
    padding: 12px 15px;
    margin: 10px 0 20px;
    border-radius: 8px;
    border: 1px solid #cccccc;
    background-color: #fafafa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 15px;
    transition: border 0.3s ease, box-shadow 0.3s ease;
    color: #222;
}

form textarea {
    min-height: 120px;
    resize: vertical;
}

form textarea:focus, form input[type="text"]:focus {
    border-color: #888888;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
    outline: none;
}

form input[type="submit"] {
    background-color: #222222;
    color: white;
    border: none;
    padding: 12px 26px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #000000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

form input[type="submit"]:active {
    transform: scale(0.97);
}

.add-to-cart-button, .edit-product-button {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 15px;
            color: black;
            border-radius: 5px;
            text-decoration: underline;
        }

        .continue-shopping {
            display: inline-block;
            margin-top: 40px;
            text-decoration: none;
            color:rgb(3, 3, 3);
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if ($product): ?>
        <div class="product-detail">
            <!-- Linkerkant -->
            <div style="flex: 1;">
                <img id="main-image" src="images/<?php echo htmlspecialchars($afbeeldingen[0] ?? 'placeholder.png'); ?>" alt="Productafbeelding">

                <div class="thumbnail-list">
                    <?php foreach ($afbeeldingen as $afbeelding): ?>
                        <img src="images/<?php echo htmlspecialchars($afbeelding); ?>" alt="Thumbnail" class="thumbnail">
                    <?php endforeach; ?>
                </div>

                <!-- Beoordelingsformulier -->
                <h2>Laat een beoordeling achter</h2>
                <form method="post" action="">
                    <label>Naam:</label><br>
                    <input type="text" name="naam" required><br>

                    <label>Beoordeling:</label><br>
                    <textarea name="beoordeling" rows="4" required></textarea><br>

                    <input type="submit" name="submit_review" value="Verstuur beoordeling">
                </form>

                <!-- Beoordelingen -->
                <h2>Beoordelingen</h2>
                <?php
                $sql_reviews = "SELECT naam, beoordeling, datum FROM product_reviews WHERE product_id = $id ORDER BY datum DESC";
                $result_reviews = $conn->query($sql_reviews);
                if ($result_reviews->num_rows > 0):
                    while ($review = $result_reviews->fetch_assoc()):
                ?>
                    <div class="review">
                        <strong><?php echo htmlspecialchars($review['naam']); ?></strong> -
                        <small><?php echo htmlspecialchars($review['datum']); ?></small>
                        <p><?php echo nl2br(htmlspecialchars($review['beoordeling'])); ?></p>
                    </div>
                <?php
                    endwhile;
                else:
                    echo "<p>Er zijn nog geen beoordelingen.</p>";
                endif;
                ?>
            </div>

            <!-- Rechterkant -->
            <div class="product-info" style="flex: 1;">
                <h1><?php echo htmlspecialchars($product['naam']); ?></h1>
                <p>€<?php echo htmlspecialchars($product['prijs']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($product['beschrijving'])); ?></p>

                <a href="winkelwagen.php?id=<?php echo htmlspecialchars($id); ?>" class="add-to-cart-button">+<i class='fas fa-shopping-cart'></i></a>
                <a href="wishlist.php?id=<?php echo htmlspecialchars($id); ?>" class="add-to-cart-button">+<i class='fas fa-heart'></i></a>
                <br><br>
                <a href="index.php" class="continue-shopping"><i class='fas fa-backward-fast'></i> Terug naar producten</a>

                <?php $isAdmin = true; if ($isAdmin): ?>
                    <br><br>
                    <a href="admin.php?id=<?php echo htmlspecialchars($id); ?>" class="edit-product-button">Product bewerken</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="image-overlay" id="image-overlay">
            <span class="close-button" id="close-overlay">&times;</span>
            <img id="overlay-image" src="" alt="Vergrote afbeelding">
        </div>

        <script>
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.getElementById('main-image');
            const overlay = document.getElementById('image-overlay');
            const overlayImage = document.getElementById('overlay-image');
            const closeOverlay = document.getElementById('close-overlay');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', () => {
                    mainImage.src = thumb.src;
                });
            });

            mainImage.addEventListener('click', () => {
                overlayImage.src = mainImage.src;
                overlay.style.display = 'flex';
            });

            closeOverlay.addEventListener('click', () => {
                overlay.style.display = 'none';
            });

            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.style.display = 'none';
                }
            });
        </script>
    <?php else: ?>
        <p>Product niet gevonden.</p>
    <?php endif; ?>

</div>
</body>
<?php include 'footer.php'; ?>

</html>
