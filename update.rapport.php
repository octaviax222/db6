<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {
    // Connexion à la base de données
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    echo "Connexion à la base de données réussie<br>";

     // Vérifier si 'idRapport' existe dans $_POST
     if (!isset($_POST['idRapport'])) {
        die("Erreur : L'ID du rapport est manquant.");
    }

    // Récupérer les valeurs du formulaire
    $idmod = $_POST['idRapport'];
    $evaluationTraitement = $_POST['evaluationTraitement'];
    $observationClinique = $_POST['observationClinique'];

    $sqlCheck = "SELECT rapportpatient.IdRapport
			FROM rapportpatient 
			JOIN visite v ON rapportpatient.IdRapport = v.IdRapport
			JOIN encode e ON v.IdVisite = e.IdVisite
			WHERE e.NumeroInami = :numeroInami AND rapportpatient.IdRapport = :idmod";

    $stmt = $base->prepare($sqlCheck);
    $stmt->bindParam(":numeroInami", $numeroInami);
    $stmt->bindParam(":idmod", $idmod);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $updateFields = [];
        $params[':idmod'] = $idmod;

        if (empty($idmod) || !is_numeric($idmod)) {
            die("Erreur : L'ID du rapport est manquant ou invalide.");
        }

        // Déterminer quelle colonne mettre à jour
        if (!empty($evaluationTraitement) && empty($observationClinique)) {
            // Si seule l'évaluation est fournie
            $sqlUpdate = "UPDATE rapportpatient SET evaluationTraitement = '$evaluationTraitement' WHERE idRapport = :idmod";
        } elseif (empty($evaluationTraitement) && !empty($observationClinique)) {
            // Si seule l'observation clinique est fournie
            $sqlUpdate = "UPDATE rapportpatient SET observationClinique = '$observationClinique' WHERE idRapport = :idmod";
        } elseif (!empty($evaluationTraitement) && !empty($observationClinique)) {
            // Si les deux sont fournies
            $sqlUpdate = "UPDATE rapportpatient SET evaluationTraitement = '$evaluationTraitement', observationClinique = '$observationClinique' WHERE idRapport = :idmod";
        } else {
            // Aucun des champs n'est fourni
            echo "Aucune donnée à mettre à jour.";
            exit;
        }

        $stmtUpdate = $base->prepare($sqlUpdate);
        $stmtUpdate->execute($params);
        echo "<h3>Le rapport avec l'ID $idmod a été modifié avec succès.</h3>";
        header("home.rapport.html");

    } else {
        // Affichage d'une alerte en cas d'erreur
        echo "<script>
            alert('Erreur : Ce rapport ne vous appartient pas ou n\'existe pas.');
            window.location.href = 'home.rapport.html';
        </script>";
        exit;
    }
} catch (Exception $e) {
    // Afficher l'erreur
    die('Erreur : ' . $e->getMessage());
}

if ($base ==TRUE){
    header("Location:home.rapport.html");
}
?>