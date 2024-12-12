<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
$numeroInami = $_SESSION['numeroInami']; // prise de l'identifiant de l'infirmière

try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');

    $sql = "SELECT rapportpatient.*, patient.nom ,patient.prenom
    FROM rapportpatient 
    JOIN visite v ON rapportpatient.IdRapport = v.IdRapport -- Jointure entre 'rapportpatient' et 'visite'
    JOIN encode e ON v.IdVisite = e.IdVisite -- Jointure avec la table 'encode'
    JOIN patient ON e.NumeroNiss = patient.NumeroNiss -- Jointure pour récupérer le nom du patient
    JOIN prestataire p ON e.NumeroInami = p.NumeroInami -- Jointure avec 'prestataire'
    LEFT JOIN realise r ON v.IdVisite = r.IdVisite -- Jointure facultative avec 'realise'
    WHERE e.NumeroInami = :numeroInami;";
    
    
    $stmt = $base->prepare($sql);
    $stmt->bindParam(":numeroInami", $numeroInami);
    $stmt->execute();

    echo "Le nombre de rapports dans la base de données est : <strong>" . $stmt->rowCount() . "</strong><br><br>";
    $rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rapports)) {
        echo "<p>Aucun rapport trouvé pour NumeroInami : $numeroInami</p>";
    } else {
        foreach ($rapports as $ligne) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($ligne['idRapport']) . "</td>";
            echo "<td>" . htmlspecialchars($ligne['nom']) . "</td>"; // Affiche le nom du patient
            echo "<td>" . htmlspecialchars($ligne['prenom']) . "</td>";
            echo "<td>" . htmlspecialchars($ligne['evaluationTraitement']) . "</td>";
            echo "<td>" . htmlspecialchars($ligne['observationClinique']) . "</td>";
            
            echo "<td><a href='supprimer.rapport.html?id=" . htmlspecialchars($ligne['idRapport']) . "' class='btn btn-danger btn-sm'>Supprimer</a></td>";
            echo "</tr>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
