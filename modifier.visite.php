<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {

    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    echo "Connexion à la base de données réussie<br>";

    $idVisite = $_POST['idVisite'];
    $date_visite = $_POST['date_visite'];
    $frequence = $_POST['frequence'];
    $description = $_POST['description'];
    $heure = $_POST['heure'];
    $idInamiTypeSoins = $_POST['idInamiTypeSoins'];

    $sql = "SELECT v.*
    FROM visite v
    JOIN encode e ON v.idVisite = e.idVisite
    WHERE e.numeroInami = :numeroInami AND e.idVisite = :idVisite";

    $stmt = $base->prepare($sql);
    $stmt->bindParam(":numeroInami", $numeroInami);
    $stmt->bindParam(":idVisite", $idVisite);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $updateFields = [];
        $params = [':idVisite' => $idVisite];

        if (!empty($date_visite)) {
            $updateFields[] = "dateR = :dateR";
            $params[':dateR'] = $date_visite;
        }
        if (!empty($frequence)) {
            $updateFields[] = "frequence = :frequence";
            $params[':frequence'] = $frequence;
        }
        if (!empty($description)) {
            $updateFields[] = "description = :description";
            $params[':description'] = $description;
        }
        if (!empty($heure)) {
            $updateFields[] = "heure = :heure";
            $params[':heure'] = $heure;
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

        if ($idInamiTypeSoins == 425110) {
        
            // Si le type de soin est "toilette", redirige vers la page Katz
            header("Location: page.echelle.katz.html");
            exit;
        }

        if (count($updateFields) > 0) {
            $sqlUpdate = "UPDATE visite SET " . implode(', ', $updateFields) . " WHERE idVisite = :idVisite";
            $stmtUpdate = $base->prepare($sqlUpdate);
            $stmtUpdate->execute($params);
            echo "<h3>La visite numéro $idVisite a été modifiée avec succès.</h3>";
            header("Location:home.visite.html");
            exit;
           
        } else {
            echo "Aucune donnée à mettre à jour.";
            header("Location:home.visite.html");
            exit;
        }

    } else {
        // Affichage d'une alerte en cas d'erreur
        echo "<script>
            alert('Erreur : Cette visite ne vous appartient pas ou n\'existe pas.');
            window.location.href = 'home.visite.html';
        </script>";
        exit;
    }
} catch (Exception $e) {
   
    die('Erreur : ' . $e->getMessage());
}
?>