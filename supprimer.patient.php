<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {
	
	$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
	$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	

	$sqlCheck = "SELECT DISTINCT patient.* FROM patient
				JOIN encode e ON patient.numeroNiss = e.numeroNiss 
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
	<title>Aucun patient disponible</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
	<div class="container mt-5">
		<div class="alert alert-warning text-center" role="alert">
			<h4>Aucun patient n\'est encodé pour le moment.</h4>
		</div>
		<div class="text-center mt-3">
			<a href="home.patient.html" class="btn btn-primary">Retour au menu de la gestion des patients</a>
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
            <a href="home.patient.html" style="color: white; text-decoration: none;">Retour</a>
        </button>
    </div>
    <title>Supprimer un Patient</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Supprimer un Patient</h1>
        
        <form action="supprimer.patient.php" method="post" onsubmit="return confirmDelete()">
            <div class="form-group">
                <label for="numeroNiss">Numéro NISS du Patient :</label>
                <input type="text" name="numeroNiss" id="numeroNiss" class="form-control" required placeholder="Entrez le numéro NISS">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-danger">Supprimer</button>
                <a href="afficher.patient.php" class="btn btn-secondary">Afficher les patients</a>
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
// Initialisation de la variable pour éviter l'erreur
$supp = isset($_POST['numeroNiss']) ? $_POST['numeroNiss'] : null;

try {
	if ($supp !== null){

		$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT patient.* FROM patient
				JOIN encode e ON patient.numeroNiss = e.numeroNiss 
                WHERE e.numeroInami = :numeroInami AND patient.numeroNiss = :supp";

		// Préparer la requête
		$stmt = $base->prepare($sql);
				
		// Exécuter la requête
		$stmt->execute([':numeroInami' => $numeroInami, ':supp' => $supp]);

			if ($stmt ->rowCount() > 0) {
				$deleteSql = "delete from patient where numeroNiss= :supp";
				$deleteStmt = $base->prepare($deleteSql);
				$deleteStmt->execute([':supp' => $supp]);

				echo "<br>le client ".$supp." est supprimé";
				header("location:afficher.patient.php");
				exit;
			} else {
				// Affichage d'une alerte en cas d'erreur
				echo "<script>
					alert('Erreur : Ce patient ne vous appartient pas ou n\'existe pas.');
					window.location.href = 'afficher.patient.php';
				</script>";
				exit;
			}
	}		

}catch (PDOException $e) {
		echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
		exit;
	}

?>