<?php
try {

    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    echo "Connexion à la base de données réussie<br>";

    $idVisite = $_POST['idVisite'];
    $date_visite = $_POST['date_visite'];
    $frequence = $_POST['frequence'];
    $description = $_POST['description'];
    $heure = $_POST['heure'];
    $idCalendrier = $_POST['idCalendrier'];
    $idRapport = $_POST['idRapport'];

    $updateFields = [];

    if (!empty($idVisite)) {
        $updateFields[] = "idVisite = '$idVisite'";
    }
    if (!empty($date_visite)) {
        $updateFields[] = "dateR = '$date_visite'";
    }
    if (!empty($frequence)) {
        $updateFields[] = "frequence = '$frequence'";
    }
    if (!empty($description)) {
        $updateFields[] = "description = '$description'";
    }
    if (!empty($heure)) {
        $updateFields[] = "heure = '$heure'";
    }
    if (!empty($idCalendrier)) {
        $updateFields[] = "idCalendrier = '$idCalendrier'";
    }
    if (!empty($idRapport)) {
        $updateFields[] = "idRapport = $idRapport";
    }
    
    if (count($updateFields) > 0) {
        $sql = "UPDATE visite SET " . implode(', ', $updateFields) . " WHERE idVisite = $idVisite";
    
        echo $sql;
        $base->exec($sql);

    } else {
        echo "Aucune donnée à mettre à jour.";
        exit;
    }

    $base->exec($sql);

    echo "<h3>La visite numéro $idVisite a été modifiée avec succès.</h3>";
} catch (Exception $e) {
   
    die('Erreur : ' . $e->getMessage());
}
?>