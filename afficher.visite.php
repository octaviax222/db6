<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');

    $sql = "SELECT v.*, p.nom AS patientNom, p.prenom AS patientPrenom
    FROM visite v 
    JOIN encode e ON v.IdVisite = e.idVisite
    JOIN patient p ON e.numeroNISS = p.numeroNISS
    LEFT JOIN realise r ON v.IdVisite = r.idVisite  -- Jointure avec la table 'Realise'
    WHERE e.numeroInami = :numeroInami";

    $stmt = $base->prepare($sql);
    $stmt->bindParam(":numeroInami", $numeroInami);
    $stmt->execute();
    
    while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $idVisite = htmlspecialchars($ligne['idVisite']);
        $patientNom = htmlspecialchars($ligne['patientNom']);
        $patientPrenom = htmlspecialchars($ligne['patientPrenom']);
        $date_visite = htmlspecialchars($ligne['dateR']);
        $description = htmlspecialchars($ligne['description']);
        $frequence = htmlspecialchars($ligne['frequence']);
        $heure = htmlspecialchars($ligne['heure']);

        //$idRapport = -1;
        if (is_null($ligne['idRapport'])){
            $idRapport = "";
        }else{
            $idRapport = htmlspecialchars($ligne['idRapport']);
        }
        
        echo "<tr>";
        echo "<td>$idVisite</td>";
        echo "<td>$patientNom</td>";
        echo "<td>$patientPrenom</td>";
        echo "<td>$date_visite</td>";
        echo "<td>$description</td>";
        echo "<td>$frequence</td>";
        echo "<td>$heure</td>";
        echo "<td>$idRapport</td>";   
        echo "<td><a href='supprimer.html?chkid=$idVisite' class='btn btn-danger btn-sm'>Supprimer</a></td>";
        echo "</tr>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}

?>