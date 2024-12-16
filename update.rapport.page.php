<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {
	
		$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		

		$sqlCheck = "SELECT DISTINCT rapportpatient.IdRapport 
                 FROM rapportpatient 
                 JOIN visite v ON rapportpatient.IdRapport = v.IdRapport
                 JOIN encode e ON v.IdVisite = e.idVisite
                 WHERE e.numeroInami = :numeroInami";
			$stmtCheck = $base->prepare($sqlCheck);
			$stmtCheck->execute([':numeroInami' => $numeroInami]);

			// Si le rapport appartient à l'infirmière, procéder à la suppression
			if ($stmtCheck->rowCount() == 0) {

				echo '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Aucun rapport disponible</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-warning text-center" role="alert">
                <h4>Aucun rapport n\'est disponible pour le moment.</h4>
            </div>
            <div class="text-center mt-3">
                <a href="home.rapport.html" class="btn btn-primary">Retour au menu de la gestion des rapports</a>
            </div>
        </div>
    </body>
    </html>';
    exit;
			}
			
	}
 catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <div class="col">
        <button class="btn btn-primary" style="margin-top: 32px;">
            <a href="home.rapport.html" style="color: white; text-decoration: none;">Retour</a>
        </button>
    </div>
    <title>Modification d'un rapport</title> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Modification d'un rapport</h1>
        
        <!-- Formulaire pour rechercher un rapport par ID -->
        <form action="" method="GET" class="mb-4">
            <div class="form-group">
                <label for="searchRapport">Entrez l'ID du rapport à modifier :</label>
                <input type="text" class="form-control" id="searchRapport" name="searchRapport" placeholder="ID du rapport" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Modifier</button>
                <a href="afficher.rapport.php" class="btn btn-secondary">Afficher les rapports</a>
                </div>
        </form>

        <?php
        if (isset($_GET['searchRapport']) && !empty($_GET['searchRapport'])) {
            $searchRapport = $_GET['searchRapport']; // ID du rapport à rechercher

            try {
                // Connexion à la base de données
                $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Requête pour récupérer les informations du rapport
                $stmt = $base->prepare("SELECT * FROM rapportpatient WHERE idRapport =?");
                $stmt->execute([$searchRapport]);
                $rapport = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($rapport) {
                    // Formulaire pré-rempli avec les informations du rapport
        ?>
                    <form action="update.rapport.php" method="post">
                        <div class="form-group">
                            <label for="idRapport">ID du rapport</label>
                            <input type="text" class="form-control" id="idRapport" name="idRapport" value="<?php echo htmlspecialchars($rapport['idRapport']); ?>" readonly>
                        </div>
  
                        <!-- Évaluation du traitement -->
                        <div class="form-group">
                            <label for="evaluationTraitement">Évaluation du traitement</label>
                            <textarea class="form-control" id="evaluationTraitement" name="evaluationTraitement" rows="3" placeholder="Entrez l'évaluation du traitement"><?php echo htmlspecialchars($rapport['evaluationTraitement']); ?></textarea>
                        </div>
                        
                        <!-- Observation clinique -->
                        <div class="form-group">
                            <label for="observationClinique">Observation clinique</label>
                            <textarea class="form-control" id="observationClinique" name="observationClinique" rows="3" placeholder="Entrez l'observation clinique"><?php echo htmlspecialchars($rapport['observationClinique']); ?></textarea>
                        </div>
                        
                        <!-- Bouton de soumission -->
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </form>
        <?php
                } else {
                    echo "<p>Rapport non trouvé.</p>";
                }
            } catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }
        ?>
    </div>
</body>
</html>
