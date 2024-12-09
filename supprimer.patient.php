<?php

session_start();
$numeroInami = $_SESSION['numeroInami']; 
$supp=$_POST['numeroNiss'];

if(isset($supp))
{	
	try{
		echo "ok";
		$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		echo "Connexion réussie à la base de données<br>";

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
				header("location:afficher.patient.html");
				exit;
			} else {
				// Affichage d'une alerte en cas d'erreur
				echo "<script>
					alert('Erreur : Ce patient ne vous appartient pas ou n\'existe pas.');
					window.location.href = 'afficher.patient.html';
				</script>";
				exit;
			}

	} catch (PDOException $e) {
		echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
		exit;
	}
}
?>