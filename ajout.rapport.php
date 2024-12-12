<?php
    // Connexion à la base de données
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');

    // Récupération des données du formulaire
    $evaluationTraitement = $_POST['evaluationTraitement'];
    $observationClinique = $_POST['observationClinique'];
    $visites = $_POST['visites'];  // Récupère les visites sélectionnées
    echo "ok";
    // Insertion du rapport dans la table rapportpatient


    $sql = "INSERT INTO rapportpatient(idRapport, evaluationTraitement, observationClinique) VALUES(NULL, '$evaluationTraitement','$observationClinique')";
    //$stmt = $base->prepare($sql);
    $Resultat = $base->exec($sql);
    // Récupération de l'ID du rapport nouvellement créé
    $idRapport = $base->lastInsertId();
    echo "L'ID du rapport inséré est : " . $idRapport;

    // Mise à jour des visites sélectionnées avec l'ID du rapport
    if (!empty($visites)) {
        // Assurez-vous que $visites est un tableau et bouclez à travers les visites
        foreach ($visites as $visite) {
            // Préparer la mise à jour pour chaque visite
            $stmt = $base->prepare("UPDATE visite SET idRapport = :idRapport WHERE idVisite = :idVisite");
            $stmt->bindParam(':idRapport', $idRapport);
            $stmt->bindParam(':idVisite', $visite);
        
            // Exécuter la requête
            $stmt->execute();
        }
    // Confirmation de la mise à jour
    echo "Les visites ont été associées au rapport avec succès!";
    } else {
        echo "Aucune visite sélectionnée.";
    }

    // Redirection après l'insertion
    
    if ($Resultat ==TRUE){
        header("Location:home.rapport.html");
    }
?>