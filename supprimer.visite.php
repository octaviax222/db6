<?php

session_start();
$numeroInami = $_SESSION['numeroInami']; 
$supp=$_POST['idVisite'];

if(isset($supp))
{	
	try{
	echo "ok";
	$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
	echo "Connexion réussie à la base de données<br>";
	
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
 
} catch (PDOException $e) {
	echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
	exit;
}
}
?>