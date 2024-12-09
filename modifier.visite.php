<?php
try {

    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    echo "Connexion à la base de données réussie<br>";

    $idVisite = $_POST['idVisite'];
    $date_visite = $_POST['date_visite'];
    $frequence = $_POST['frequence'];
    $description = $_POST['description'];
    $heure = $_POST['heure'];
    $idInamiTypeSoins = $_POST['idInamiTypeSoins'];

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
    if (!empty($idInamiTypeSoins)) {
        // Jointure entre la table visite et soins pour mettre à jour idInamiTypeSoins dans la table soins
        $sqlSoin = "
            UPDATE soins s
            INNER JOIN realise r ON r.idSoins = s.idSoins  -- Jointure avec la table realise sur idSoins
            INNER JOIN visite v ON v.idVisite = r.idVisite  -- Jointure avec la table visite sur idVisite
            SET s.idInamiTypeSoins = :idInamiTypeSoins
            WHERE v.idVisite = :idVisite
        ";

        // Préparation de la requête
        $stmtSoin = $base->prepare($sqlSoin);

        // Exécution de la requête de mise à jour
        $stmtSoin->execute([
            ':idInamiTypeSoins' => $idInamiTypeSoins,
            ':idVisite' => $idVisite  // Nous ciblons la visite spécifique
        ]);}

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