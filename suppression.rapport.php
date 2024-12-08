<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

$supp=$_POST['numeroRapport'];

try {
	if(isset($supp))
	{	
		$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		echo "Connexion réussie à la base de données<br>";

		$sql = "SELECT rapportpatient.*
			FROM rapportpatient 
			JOIN visite v ON rapportpatient.IdRapport = v.IdRapport
			JOIN encode e ON v.IdVisite = e.IdVisite
			WHERE e.NumeroInami = :numeroInami AND rapportpatient.IdRapport = :supp";
			$stmt = $base->prepare($sql);
			$stmt->execute([':numeroInami' => $numeroInami, ':supp' => $supp]);

			// Si le rapport appartient à l'infirmière, procéder à la suppression
			if ($stmt->rowCount() > 0) {
				$deleteSql = "DELETE FROM rapportpatient WHERE IdRapport = :supp";
				$deleteStmt = $base->prepare($deleteSql);
				$deleteStmt->execute([':supp' => $supp]);
				echo "<br>Le rapport " . $supp . " est supprimé";
				header("Location:home.rapport.html");
				exit;
			} else {
				echo "<script>
				alert('Erreur : Ce rapport ne vous appartient pas ou n\'existe pas.');
				window.location.href = 'home.rapport.html'; // Redirection après erreur
			</script>";
				exit;
			}
	}
}
catch{
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

?>