<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {

    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    echo "Connexion à la base de données réussie<br>";

    $idmod = $_POST['idmod'];
    $numeroNISS = $_POST['numeroNiss'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateDeNaissance = $_POST['dateDeNaissance'];
    $rue = $_POST['rue'];
    $numeroDomicile = $_POST['numeroDomicile'];
    $ville = $_POST['ville'];
    $sexe = $_POST['sexe'];
    $numeroInamiMedecin = $_POST['numeroInamiMedecin']; //medecin
    $numeroAssu=$_POST['idAssurabilite'];

    $sql = "SELECT p.* 
        FROM patient p
        JOIN encode e ON p.numeroNiss = e.numeroNiss 
        WHERE e.numeroInami = :numeroInami AND e.numeroNiss = :idmod";

    $stmt = $base->prepare($sql);
    $stmt->bindParam(":numeroInami", $numeroInami);
    $stmt->bindParam(":idmod", $idmod);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $updateFields = [];
        $params[':idmod'] = $idmod;

        
        if (!empty($nom)) {
            $updateFields[] = "nom = :nom";
            $params[':nom'] = $nom;
        }
        if (!empty($prenom)) {
            $updateFields[] = "prenom = :prenom";
            $params[':prenom'] = $prenom;
        }
        if (!empty($dateDeNaissance)) {
            $updateFields[] = "dateDeNaissance = :dateDeNaissance";
            $params[':dateDeNaissance'] = $dateDeNaissance;
        }
        if (!empty($rue)) {
            $updateFields[] = "rue = :rue";
            $params[':rue'] = $rue;
        }
        if (!empty($numeroDomicile)) {
            $updateFields[] = "numeroDomicile = :numeroDomicile";
            $params[':numeroDomicile'] = $numeroDomicile;
        }
        if (!empty($ville)) {
            $updateFields[] = "ville = :ville";
            $params[':ville'] = $ville;
        }
        if (!empty($sexe)) {
            $updateFields[] = "sexe = :sexe";
            $params[':sexe'] = $sexe;
        }
        if (!empty($numeroInamiMedecin)) {
            $updateFields[] = "numeroInamiMedecin = :numeroInamiMedecin";
            $params[':numeroInamiMedecin'] = $numeroInamiMedecin;
        }
        if (!empty($numeroAssu)) {
            $updateFields[] = "idAssurabilite = :numeroAssu";
            $params[':numeroAssu'] = $numeroAssu;
        }
        
        if (count($updateFields) > 0) {
            $sqlUpdate = "UPDATE patient SET " . implode(', ', $updateFields) . " WHERE numeroNISS = :idmod";
        
            $stmtUpdate = $base->prepare($sqlUpdate);
            $stmtUpdate->execute($params);
            echo "<h3>Le rapport avec l'ID $idmod a été modifié avec succès.</h3>";
            header("Location:home.patient.html");
        } else {
            echo "Aucune donnée à mettre à jour.";
            exit;
        }

        // 2. Mettre à jour le numeroNISS séparément si nécessaire
    if (!empty($numeroNISS) && $numeroNISS != $idmod) {
        $sqlUpdateNISS = "UPDATE patient SET numeroNISS = :newNumeroNiss WHERE numeroNISS = :idmod";
        $stmtUpdateNISS = $base->prepare($sqlUpdateNISS);
        $stmtUpdateNISS->execute([
            ':newNumeroNiss' => $numeroNISS,
            ':idmod' => $idmod
        ]);
    }

    echo "<h3>Le rapport avec l'ID $idmod a été modifié avec succès.</h3>";
    header("Location: home.patient.html");
    exit;

    } else {
        // Affichage d'une alerte en cas d'erreur
        echo "<script>
            alert('Erreur : Ce patient ne vous appartient pas ou n\'existe pas.');
            window.location.href = 'home.patient.html';
        </script>";
        exit;
    }
} catch (Exception $e) {
   
    die('Erreur : ' . $e->getMessage());
}
?>