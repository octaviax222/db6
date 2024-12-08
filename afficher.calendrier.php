<?php

try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit();
}

session_start();
$numeroInami = $_SESSION['numeroInami'];

// Récupérer le mois et l'année via GET ou utiliser la date actuelle
$mois = isset($_GET['mois']) ? $_GET['mois'] : date('m');
$annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');

// Requête pour récupérer les visites et les informations du patient
$query = $base->prepare("
    SELECT 
        v.IdVisite, v.DateR, v.Heure, v.Description,
        p.nom AS nom, p.prenom AS prenom, p.rue, p.numeroDomicile, p.ville,
        r.idInamiTypeSoin
    FROM visite v
    JOIN encode e ON v.IdVisite = e.idVisite
    JOIN patient p ON e.numeroNISS = p.numeroNISS
    LEFT JOIN realise r ON v.IdVisite = r.idVisite  -- Jointure avec la table 'Realise'
    WHERE MONTH(v.DateR) = :mois AND YEAR(v.DateR) = :annee
    AND e.numeroInami = :numeroInami
");

$query->execute(['mois' => $mois, 'annee' => $annee, 'numeroInami' => $numeroInami]);
$visites = $query->fetchAll(PDO::FETCH_ASSOC);


// Organiser les visites par jour
$events = [];
foreach ($visites as $visite) {
    $jour = date('j', strtotime($visite['DateR']));
    $events[$jour][] = $visite;
}

// Déterminer le nombre de jours dans le mois et le premier jour du mois
$nbJours = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
$premierJour = date('N', strtotime("$annee-$mois-01"));

// Calculer les mois précédent et suivant pour la navigation
$moisPrecedent = $mois - 1 ?: 12;
$anneePrecedente = $mois == 1 ? $annee - 1 : $annee;
$moisSuivant = $mois + 1 > 12 ? 1 : $mois + 1;
$anneeSuivante = $mois == 12 ? $annee + 1 : $annee;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <p><button class="w3-button w3-orange w3-round"><a href="home.visite.html">RETOUR</a> </button></p>
    <title>Calendrier des Visites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    
    <style>
        body {
            background-color: white;
            padding-top: 50px;
        }

        .container {
            max-width: 1000px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #007bff;
            text-align: center;
        }

        .calendar-nav {
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .calendar-nav a {
            font-size: 1.2rem;
            margin: 0 15px;
            color: #007bff;
            text-decoration: none;
        }

        .calendar-nav a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            vertical-align: top;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .visite {
            background-color: #e0f7fa;
            margin: 5px;
            padding: 5px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .visite p {
            margin: 0;
        }

        .calendar-day {
            height: 120px;
            position: relative;
        }

        .calendar-day .date {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }

        .calendar-day:hover {
            background-color: #e2e6ea;
            cursor: pointer;
        }

    </style>
</head>
<body>

<h1>Calendrier des Visites - <?php echo date('F Y', strtotime("$annee-$mois-01")); ?></h1>
<div class="calendar-nav">
    <a href="?mois=<?php echo $moisPrecedent; ?>&annee=<?php echo $anneePrecedente; ?>">Mois Précédent</a> |
    <a href="?mois=<?php echo $moisSuivant; ?>&annee=<?php echo $anneeSuivante; ?>">Mois Suivant</a>
</div>

    <table>
    <tr>
        <th>Lundi</th>
        <th>Mardi</th>
        <th>Mercredi</th>
        <th>Jeudi</th>
        <th>Vendredi</th>
        <th>Samedi</th>
        <th>Dimanche</th>
    </tr>
    <tr>
        <?php
        // Afficher les cases vides jusqu'au premier jour du mois
        for ($i = 1; $i < $premierJour; $i++) {
            echo '<td></td>';
        }

        // Afficher chaque jour du mois
        for ($jour = 1; $jour <= $nbJours; $jour++) {
            echo '<td>';
            echo "<strong>$jour</strong>";

            // Afficher les visites pour ce jour
            if (isset($events[$jour])) {
                foreach ($events[$jour] as $visite) {
                    echo "<div class='visite'>";
                    echo "<p><strong>Patient : </strong>" . htmlspecialchars($visite['nom']) . " " . htmlspecialchars($visite['prenom']) . "</p>";
                    echo "<p><strong>Adresse : </strong>" . htmlspecialchars($visite['rue']) . ", " . htmlspecialchars($visite['numeroDomicile']) . ", " . htmlspecialchars($visite['ville']) . "</p>";
                    echo "<p><strong>Heure :  </strong>" . date('H:i', strtotime($visite['Heure'])) . "</p>";
                    echo "<p><strong>Description :  </strong>" . htmlspecialchars($visite['Description']) . "</p>";

                    // Afficher les soins associés
                    if (!empty($visite['idInamiTypeSoin'])) {
                        echo "<p><strong>Soin : </strong>" . htmlspecialchars($visite['idInamiTypeSoin']) . "</p>";
                    }

                    echo "</div>";
                }
            }
            echo '</td>';

            // Aller à la ligne chaque dimanche
            if (($jour + $premierJour - 1) % 7 == 0) {
                echo '</tr><tr>';
            }
        }

        // Compléter les cases jusqu'à la fin de la semaine
        for ($i = ($jour + $premierJour - 1) % 7; $i < 7 && $i != 0; $i++) {
            echo '<td></td>';
        }
        ?>
    </tr>
</table>

    </tr>
</table>

</body>
</html>
