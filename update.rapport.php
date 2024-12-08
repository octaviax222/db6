<?php
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

        if (empty($idmod) || !is_numeric($idmod)) {
            die("Erreur : L'ID du rapport est manquant ou invalide.");
        }

        // Déterminer quelle colonne mettre à jour
        if (!empty($evaluationTraitement) && empty($observationClinique)) {
            // Si seule l'évaluation est fournie
            $sql = "UPDATE rapportpatient SET evaluationTraitement = '$evaluationTraitement' WHERE idRapport = $idmod";
        } elseif (empty($evaluationTraitement) && !empty($observationClinique)) {
            // Si seule l'observation clinique est fournie
            $sql = "UPDATE rapportpatient SET observationClinique = '$observationClinique' WHERE idRapport = $idmod";
        } elseif (!empty($evaluationTraitement) && !empty($observationClinique)) {
            // Si les deux sont fournies
            $sql = "UPDATE rapportpatient SET evaluationTraitement = '$evaluationTraitement', observationClinique = '$observationClinique' WHERE idRapport = $idmod";
        } else {
            // Aucun des champs n'est fourni
            echo "Aucune donnée à mettre à jour.";
            exit;
        }

    // Exécuter la requête
    $base->exec($sql);

    echo "<h3>Le rapport avec l'ID $idmod a été modifié avec succès.</h3>";
} catch (Exception $e) {
    // Afficher l'erreur
    die('Erreur : ' . $e->getMessage());
}
?>