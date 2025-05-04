<?php
include 'connect.php';
?>
<footer>
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
                        <li><a href="<?= htmlspecialchars($link['link']) ?>" target="_blank"><?= htmlspecialchars($link['name']) ?></a></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>Geen social media links</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class='footer-bottom'>
        <p>&copy; 2024 Webshop. Alle rechten voorbehouden.</p>
    </div>
</footer>
