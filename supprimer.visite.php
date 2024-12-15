<?php

session_start();
$numeroInami = $_SESSION['numeroInami']; 

try {
	
	$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
	$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	

	$sqlCheck = "SELECT DISTINCT visite.* FROM visite
				JOIN encode e ON visite.idVisite = e.idVisite 
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
			<h4>Aucune visite n\'est encodée pour le moment.</h4>
		</div>
		<div class="text-center mt-3">
			<a href="home.visite.html" class="btn btn-primary">Retour au menu de la gestion des visites</a>
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
            <a href="home.visite.html" style="color: white; text-decoration: none;">Retour</a>
        </button>
    </div>
    <title>Supprimer une visite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Supprimer une visite</h1>
        
        <form action="supprimer.visite.php" method="post" onsubmit="return confirmDelete()">
            <div class="form-group">
                <label for="idVisite">Numéro de la visite à supprimer :</label>
                <input type="text" name="idVisite" id="idVisite" class="form-control" required placeholder="Numéro de la visite à supprimer">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-danger">Supprimer</button>
                <a href="afficher.visite.html" class="btn btn-secondary">Afficher les visites</a>
            </div>
        </form>
    </div>

    <script>
        // Message de confirmation avant la suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer ce patient ?");
        }
    </script>
</body>
</html>

<?php
$supp = isset($_POST['numeroRapport']) ? $_POST['numeroRapport'] : null;
// Traitement de la suppression après soumission du formulaire
try {
    if ($supp !== null) { // Vérifie si la variable $supp est définie
        
		$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sql = "SELECT visite.* FROM visite
				JOIN encode e ON visite.idVisite = e.idVisite 
                WHERE e.numeroInami = :numeroInami AND visite.idVisite = :supp";

		// Préparer la requête
		$stmt = $base->prepare($sql);
				
		// Exécuter la requête
		$stmt->execute([':numeroInami' => $numeroInami, ':supp' => $supp]);

			if ($stmt ->rowCount() > 0) {
				$deleteSql = "delete from visite where idVisite= :supp";
				$deleteStmt = $base->prepare($deleteSql);
				$deleteStmt->execute([':supp' => $supp]);

				echo "<br>la visite ".$supp." est supprimée";
				header("location:afficher.visite.html");
				exit;
			} else {
				// Affichage d'une alerte en cas d'erreur
				echo "<script>
					alert('Erreur : Ce patient ne vous appartient pas ou n\'existe pas.');
					window.location.href = 'afficher.visite.html';
				</script>";
				exit;
			}
 
	}
} catch (PDOException $e) {
	echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
	exit;
}

?>