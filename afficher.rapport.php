<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
$numeroInami = $_SESSION['numeroInami']; // prise de l'identifiant de l'infirmière

try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');

    $sql = "SELECT rapportpatient.*
    FROM rapportpatient 
    JOIN visite v ON rapportpatient.IdRapport = v.IdRapport -- Jointure entre 'rapportpatient' et 'visite'
    JOIN encode e ON v.IdVisite = e.IdVisite -- Jointure avec la table 'encode'
    JOIN prestataire p ON e.NumeroInami = p.NumeroInami -- Jointure avec 'prestataire'
    LEFT JOIN realise r ON v.IdVisite = r.IdVisite -- Jointure facultative avec 'realise'
    WHERE e.NumeroInami = :numeroInami;";

    $stmt = $base->prepare($sql);
    $stmt->bindParam(":numeroInami", $numeroInami);
    $stmt->execute();

    echo "Le nombre de rapports dans la base de données est : <strong>" . $stmt->rowCount() . "</strong><br><br>";
    
    while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $idRapport = htmlspecialchars($ligne['idRapport']);
        $evaluationTraitement = htmlspecialchars($ligne['evaluationTraitement']);
        $observationClinique = htmlspecialchars($ligne['observationClinique']);
        
        echo "<tr>";
        echo "<td>$idRapport</td>";
        echo "<td>$evaluationTraitement</td>";
        echo "<td>$observationClinique</td>";
        echo "<td><a href='supprimer.rapport.html?id=$idRapport' class='btn btn-danger btn-sm'>Supprimer</a></td>";
        echo "</tr>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
