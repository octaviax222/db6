<?php
session_start();
$numeroInami = $_SESSION['numeroInami']; // Identifiant de l'infirmière

// Récupère le numéro de la facture depuis le formulaire
$supp = $_POST['numeroFacture'];

// Vérifie si le numéro de facture est défini dans la requête POST
if (isset($_POST['numeroFacture'])) {
    $supp = $_POST['numeroFacture'];
} else {
    echo "Erreur : Numéro de facture non spécifié.";
    exit;
}

echo "Numéro de la facture : " . $supp . "<br>";
echo "Numéro INAMI : " . $numeroInami . "<br>";

if (isset($supp)) {
    try {

        $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		echo "Connexion réussie à la base de données<br>";

        $sql = "SELECT facturation.*
                FROM facturation
                JOIN soins ON facturation.idFacturation = soins.idFacturation
                JOIN realise ON soins.idInamiTypeSoin = realise.idInamiTypeSoin
                JOIN encode e ON realise.idVisite = e.idVisite
                WHERE e.numeroInami = :numeroInami AND facturation.idFacturation = :supp";
        $stmt = $base->prepare($sql);
        $stmt->execute([':numeroInami' => $numeroInami, ':supp' => $supp]);

        // Si la facture appartient à l'infirmière, procéder à la suppression
        if ($stmt->rowCount() > 0) {
            $deleteSql = "DELETE FROM facturation WHERE idFacturation = :supp";
            $deleteStmt = $base->prepare($deleteSql);
            $deleteStmt->execute([':supp' => $supp]);

            echo "<br>La facture " . $supp . " est supprimée";
            // Redirection après succès
			header("Location:home.facturation.html");
            exit;
        } else {
            // Affichage d'une alerte en cas d'erreur
            echo "<script>
                alert('Erreur : Cette facture ne vous appartient pas ou n\'existe pas.');
                window.location.href = 'home.facturation.html';
            </script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
		exit;
	}
}
?>
