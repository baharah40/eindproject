
<?php
include 'connect.php';

// Haal social media links op
$socialMediaLinks = $conn->query("SELECT name, link FROM social_media_links");
?>

<footer>
    <style>
         .social-media-links {
            padding: 20px;
            text-align: center;
        }


        .footer-section {
            flex: 1;
            padding: 10px;
            max-width: 300px;
        }

        .footer-section h2 {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .social-media-links ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-media-links ul li {
            display: inline;
        }

        .social-media-links a {
            text-decoration: none;
            color: rgb(254, 254, 254);
            font-weight: bold;
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .social-media-links a:hover {
            background-color: #007bff;
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            color: #eee;
            padding: 30px 40px;
            gap: 30px;
            font-family: Arial, sans-serif;
        }
        .footer-section p, .footer-section a, .footer-section ul, .footer-section li {
            font-size: 1rem;
            line-height: 1.5;
            color: #ddd;
            margin: 0 0 8px 0;
        }
        .footer-section a {
            color:rgb(255, 255, 255);
            text-decoration: none;
        }
        .footer-section a:hover {
            text-decoration: underline;
        }
        .footer-section ul {
            list-style: none;
            padding-left: 0;
        }
        .footer-section ul li {
            margin-bottom: 8px;
        }
        .social-media-links ul {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .faq-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .faq-list li {
            margin-bottom: 8px;
        }
        /* Footer onderaan */
        .footer-bottom {
            color: #888;
            text-align: center;
            padding: 15px 10px;
            font-size: 0.9rem;
            margin-top: 20px;
            font-family: Arial, sans-serif;
        }

        #google_translate_element {
            margin-top: 20px;
            color: #ddd;
        }
    </style>

    <div class='footer-container'>
        <div class='footer-section'>
            <h2>Retourbeleid</h2>
            <p>Niet tevreden? Retourneer binnen 30 dagen voor volledige terugbetaling of omruiling.</p>
            <p><a href='retourbeleid.php'>Bekijk retourbeleid</a></p>
        </div>

        <div class='footer-section'>
            <h2>Contact</h2>
            <p>Heb je vragen? Mail ons op: <a href='mailto:klantenservice@webshop.com'>klantenservice@webshop.com</a></p>
        </div>

        <div class='footer-section social-media-links'>
            <h2>Volg ons</h2>
            <ul>
                <?php if (isset($socialMediaLinks) && $socialMediaLinks->num_rows > 0): ?>
                    <?php while ($link = $socialMediaLinks->fetch_assoc()): ?>
                        <li><a href="<?= htmlspecialchars($link['link']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($link['name']) ?></a></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>Geen social media links</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class='footer-section'>
            <h2>FAQ</h2>
            <ul class="faq-list">
                <li><a href="faq.php#bestellingen">Hoe plaats ik een bestelling?</a></li>
                <li><a href="faq.php#verzending">Wat zijn de verzendkosten?</a></li>
                <li><a href="faq.php#retour">Hoe werkt het retourbeleid?</a></li>
                <li><a href="faq.php">Bekijk alle veelgestelde vragen</a></li>
            </ul>
        </div>
    </div>

    <div id="google_translate_element"></div>

    <script src="https://cdn.botpress.cloud/webchat/v2.5/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/05/18/16/20250518162434-PIOOPSJ9.js"></script>

    <!-- Google Translate API -->
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Initialisatie van Google Translate -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en'
            }, 'google_translate_element');
        }
    </script>

    <div class='footer-bottom'>
        <p>&copy; 2024 Webshop. Alle rechten voorbehouden.</p>
    </div>
</footer>
