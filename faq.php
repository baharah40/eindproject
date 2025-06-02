<?php
$faq = [
    "Productinformatie" => [
        [
            "question" => "Van welk materiaal zijn jullie tassen gemaakt?",
            "answer" => "Onze tassen worden gemaakt van hoogwaardige materialen zoals echt leer, vegan leer, canvas en nylon. Bij elk product staat de exacte materiaalbeschrijving vermeld."
        ],
        [
            "question" => "Zijn de tassen waterdicht?",
            "answer" => "De meeste van onze tassen zijn waterafstotend, maar niet volledig waterdicht. We raden aan om bij hevige regen een regenhoes te gebruiken."
        ],
        [
            "question" => "Hoe onderhoud ik mijn leren tas?",
            "answer" => "Gebruik een speciale leercrème om het leer soepel en glanzend te houden. Vermijd direct zonlicht en water. Raadpleeg onze onderhoudspagina voor tips per materiaalsoort."
        ],
        [
            "question" => "Hebben jullie duurzame of vegan tassen?",
            "answer" => "Ja! We hebben een speciale collectie duurzame tassen, gemaakt van gerecyclede of diervriendelijke materialen. Je vindt ze onder het filter “Duurzaam” of “Vegan”."
        ],
        [
            "question" => "Kan ik mijn laptop in jullie tassen meenemen?",
            "answer" => "Zeker! Veel van onze rugzakken, laptoptassen en handtassen hebben een speciaal laptopvak. Let op de productbeschrijving voor het maximale laptopformaat."
        ],
    ],
    "Bestellingen & Betalingen" => [
        [
            "question" => "Hoe kan ik een bestelling plaatsen?",
            "answer" => "Plaats het gewenste product in je winkelmandje, vul je gegevens in, kies een betaalmethode en bevestig je bestelling. Je ontvangt een bevestiging per e-mail."
        ],
        [
            "question" => "Welke betaalmethodes accepteren jullie?",
            "answer" => "Wij accepteren iDEAL, Bancontact, creditcard (Visa/MasterCard), PayPal, Klarna (achteraf betalen) en cadeaubonnen van onze winkel."
        ],
        [
            "question" => "Kan ik mijn bestelling nog wijzigen of annuleren?",
            "answer" => "Neem zo snel mogelijk contact met ons op. Als de bestelling nog niet is verzonden, kunnen we deze mogelijk nog aanpassen of annuleren."
        ],
    ],
    "Verzending & Levering" => [
        [
            "question" => "Wat zijn de verzendkosten?",
            "answer" => "Binnen Nederland en België is verzending gratis vanaf €50. Onder dat bedrag rekenen wij €4,95 verzendkosten."
        ],
        [
            "question" => "Hoe lang duurt de levering?",
            "answer" => "Bestellingen die op werkdagen vóór 16:00 uur worden geplaatst, worden dezelfde dag verzonden. Levering binnen NL/BE duurt meestal 1-2 werkdagen."
        ],
        [
            "question" => "Verzenden jullie ook naar het buitenland?",
            "answer" => "Ja, wij verzenden naar de meeste Europese landen. Bekijk de verzendinformatiepagina voor tarieven en levertijden per land."
        ],
        [
            "question" => "Hoe kan ik mijn bestelling volgen?",
            "answer" => "Zodra je bestelling is verzonden, ontvang je een track & trace-code per e-mail om je pakket te volgen."
        ],
    ],
    "Retourneren & Ruilen" => [
        [
            "question" => "Wat is jullie retourbeleid?",
            "answer" => "Je hebt 30 dagen bedenktijd om je bestelling te retourneren, mits ongedragen en in originele verpakking. Gebruik het retourformulier in je pakket."
        ],
        [
            "question" => "Moet ik zelf de retourkosten betalen?",
            "answer" => "Voor Nederlandse en Belgische klanten zijn retouren gratis via ons retourportaal. Voor internationale retouren gelden andere voorwaarden."
        ],
        [
            "question" => "Hoe werkt het ruilen van een artikel?",
            "answer" => "Wil je ruilen? Neem contact op met onze klantenservice. Wij zorgen voor een snelle omruiling of terugbetaling."
        ],
    ],
    "Garantie & Reparatie" => [
        [
            "question" => "Heb ik garantie op mijn tas?",
            "answer" => "Ja, op alle tassen geven wij 1 jaar garantie op productiefouten. Slijtage door gebruik valt hier niet onder."
        ],
        [
            "question" => "Mijn tas is kapot gegaan, wat nu?",
            "answer" => "Stuur ons een foto en bestelnummer via e-mail. Wij beoordelen de schade en kijken of reparatie, vervanging of terugbetaling mogelijk is."
        ],
    ],
    "Overig" => [
        [
            "question" => "Hebben jullie een fysieke winkel?",
            "answer" => "Ja! Je bent welkom in onze winkel in [stad/locatie]. Openingstijden en adres vind je op onze contactpagina."
        ],
        [
            "question" => "Bieden jullie cadeaubonnen aan?",
            "answer" => "Zeker! Je kunt digitale of fysieke cadeaubonnen kopen via onze webshop of in de winkel. Keuze uit verschillende bedragen."
        ],
        [
            "question" => "Hoe neem ik contact met jullie op?",
            "answer" => "Via e-mail (info@jouwtaswinkel.nl), telefoon of het contactformulier op onze website. We antwoorden meestal binnen 24 uur."
        ],
    ],
];

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FAQ - Taswinkel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
            background: #fafafa;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }
        h2 {
            border-bottom: 2px solid #333;
            padding-bottom: 0.5rem;
            margin-top: 2rem;
        }
        .faq-item {
            margin-bottom: 1.5rem;
        }
        .question {
            font-weight: bold;
            cursor: pointer;
            position: relative;
            padding-right: 20px;
        }
        .question::after {
            content: "+";
            position: absolute;
            right: 0;
            top: 0;
            font-size: 1.2rem;
            transition: transform 0.3s;
        }
        .question.active::after {
            content: "-";
        }
        .answer {
            display: none;
            margin-top: 0.5rem;
            padding-left: 1rem;
            border-left: 3px solid #333;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Veelgestelde Vragen (FAQ) - Taswinkel</h1>

    <?php foreach ($faq as $category => $items): ?>
        <section>
            <h2><?= htmlspecialchars($category) ?></h2>
            <?php foreach ($items as $item): ?>
                <div class="faq-item">
                    <div class="question"><?= htmlspecialchars($item['question']) ?></div>
                    <div class="answer"><?= htmlspecialchars($item['answer']) ?></div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>

    <script>
        document.querySelectorAll('.question').forEach(question => {
            question.addEventListener('click', () => {
                question.classList.toggle('active');
                const answer = question.nextElementSibling;
                if (answer.style.display === "block") {
                    answer.style.display = "none";
                } else {
                    answer.style.display = "block";
                }
            });
        });
    </script>
            <a href="index.php" class="continue-shopping">Terug naar de startpagina</a>

</body>
</html>
