<?php
session_start(); // Connexion à la session pour récupérer les informations de l'infirmière

// Connexion à la base de données
try {
    $db = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $evaluationTraitement = $_POST['evaluationTraitement'];
    $observationClinique = $_POST['observationClinique'];
    $visites = isset($_POST['visites']) ? $_POST['visites'] : [];

    try {
        // Insertion dans la table rapportpatient
        $sql = "INSERT INTO rapportpatient (idRapport, evaluationTraitement, observationClinique) 
                VALUES (NULL, :evaluationTraitement, :observationClinique)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':evaluationTraitement', $evaluationTraitement);
        $stmt->bindParam(':observationClinique', $observationClinique);
        $stmt->execute();

        // Récupération de l'ID du rapport inséré
        $idRapport = $db->lastInsertId();

        // Mise à jour des visites sélectionnées
        if (!empty($visites)) {
            $updateStmt = $db->prepare("UPDATE visite SET idRapport = :idRapport WHERE idVisite = :idVisite");
            foreach ($visites as $visite) {
                $updateStmt->bindParam(':idRapport', $idRapport);
                $updateStmt->bindParam(':idVisite', $visite);
                $updateStmt->execute();
            }
            $message = "Le rapport et les visites ont été enregistrés avec succès!";
        } else {
            $message = "Rapport ajouté, mais aucune visite sélectionnée.";
        }
        header("Location: home.rapport.html");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de l'insertion : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un rapport</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" 
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="col">
        <button class="btn btn-primary" style="margin-top: 32px;">
            <a href="home.rapport.html" style="color: white; text-decoration: none;">Retour</a>
        </button>
    </div>
    <div class="container mt-5">
        <h1 class="text-center">Ajout d'un rapport</h1>
        <form action="" method="post">
            <!-- Sélection des visites -->
            <div class="form-group">
                <label>Sélectionnez les visites à inclure dans le rapport :</label><br>
                <?php
                // Récupère les visites associées à l'infirmière et non encore liées à un rapport
                $numeroInami = $_SESSION['numeroInami'];
                $query = $db->prepare("
                    SELECT v.idVisite, v.dateR, v.description 
                    FROM visite v
                    JOIN encode e ON v.idVisite = e.idVisite
                    WHERE e.NumeroInami = :numeroInami AND v.idRapport IS NULL AND v.idVisite != 121");
                $query->bindParam(':numeroInami', $numeroInami);
                $query->execute();

                if ($query->rowCount() > 0) {
                    while ($visite = $query->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div>';
                        echo '<input type="checkbox" name="visites[]" value="' . htmlspecialchars($visite['idVisite']) . '"> ';
                        echo 'Visite ' . htmlspecialchars($visite['idVisite']) . ' - ' 
                             . htmlspecialchars($visite['dateR']) . ' - ' 
                             . htmlspecialchars($visite['description']);
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">Soit aucune visite est encodée soit toutes les visites concernent déjà un rapport</div>';
                }
                ?>
            </div>

            <!-- Évaluation du traitement -->
            <div class="form-group">
                <label for="evaluationTraitement">Évaluation du traitement</label>
                <textarea class="form-control" id="evaluationTraitement" name="evaluationTraitement" rows="3" 
                          placeholder="Entrez l'évaluation du traitement" required></textarea>
            </div>

            <!-- Observation clinique -->
            <div class="form-group">
                <label for="observationClinique">Observation clinique</label>
                <textarea class="form-control" id="observationClinique" name="observationClinique" rows="3" 
                          placeholder="Entrez l'observation clinique" required></textarea>
            </div>

            <!-- Bouton de soumission -->
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
