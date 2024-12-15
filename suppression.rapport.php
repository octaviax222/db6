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
<html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <div class="col">
        <button class="btn btn-primary" style="margin-top: 32px;">
            <a href="home.rapport.html" style="color: white; text-decoration: none;">Retour</a>
        </button>
    </div>
    <title>Supprimer un rapport</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Supprimer un rapport</h1>
        
        <form action="suppression.rapport.php" method="post" onsubmit="return confirmDelete()">
            <div class="form-group">
                <label for="numeroRapport">Numéro du rapport à supprimer :</label>
                <input type="text" name="numeroRapport" id="numeroRapport" class="form-control" required placeholder="Entrez le numéro du rapport">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-danger">Supprimer</button>
                <a href="afficher.rapport.html" class="btn btn-secondary">Afficher les rapports</a>
            </div>
        </form>
    </div>

    <script>
        // Message de confirmation avant la suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer ce rapport ?");
        }
    </script>
</body>
</html>
<?php

// Initialisation de la variable pour éviter l'erreur
$supp = isset($_POST['numeroRapport']) ? $_POST['numeroRapport'] : null;
// Traitement de la suppression après soumission du formulaire
try {
    if ($supp !== null) { // Vérifie si la variable $supp est définie
        $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si le rapport appartient à l'infirmière
        $sql = "SELECT rapportpatient.*
                FROM rapportpatient 
                JOIN visite v ON rapportpatient.IdRapport = v.IdRapport
                JOIN encode e ON v.IdVisite = e.IdVisite
                WHERE e.NumeroInami = :numeroInami AND rapportpatient.IdRapport = :supp";
        $stmt = $base->prepare($sql);
        $stmt->execute([':numeroInami' => $numeroInami, ':supp' => $supp]);

        if ($stmt->rowCount() > 0) {
            // Supprimer le rapport
            $deleteSql = "DELETE FROM rapportpatient WHERE IdRapport = :supp";
            $deleteStmt = $base->prepare($deleteSql);
            $deleteStmt->execute([':supp' => $supp]);

            echo "<div class='alert alert-success'>Le rapport $supp a été supprimé avec succès.</div>";
            header("refresh:2;url=home.rapport.html"); // Redirection après 2 secondes
            exit;
        } else {
            echo "<div class='alert alert-danger'>Erreur : Ce rapport ne vous appartient pas ou n'existe pas.</div>";
        }
    } 
} catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }

?>