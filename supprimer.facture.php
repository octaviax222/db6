<?php
session_start();
$numeroInami = $_SESSION['numeroInami']; // Identifiant de l'infirmière

try {
	
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

    $sqlCheck = "SELECT facturation.*
                FROM facturation
                JOIN soins ON facturation.idFacturation = soins.idFacturation
                JOIN realise ON soins.idSoins = realise.idSoins
                JOIN encode e ON realise.idVisite = e.idVisite
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
    <title>Aucune facture disponible</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-warning text-center" role="alert">
            <h4>Aucune facture n\'est disponible pour le moment.</h4>
        </div>
        <div class="text-center mt-3">
            <a href="home.facturation.html" class="btn btn-primary">Retour au menu de la gestion de la facturation</a>
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
            <a href="home.facturation.html" style="color: white; text-decoration: none;">Retour</a>
        </button>
    </div>
    <title>Supprimer un facture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">

        <h1 class="text-center mb-4">Supprimer une facture</h1>
        
        <form action="supprimer.facture.php" method="post" onsubmit="return confirmDelete()">
            <div class="form-group">
                <label for="numeroFacture">Numéro de la facture :</label>
                <input type="text" name="numeroFacture" id="numeroFacture" class="form-control" required placeholder="Entrez le numéro de la facture">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-danger">Supprimer</button>
                <a href="afficher.facture.php" class="btn btn-secondary">Afficher la période de facturation</a>
            </div>
        </form>
    </div>

    <script>
        // Message de confirmation avant la suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cette facture ?");
        }
    </script>
</body>
</html>

<?php
// Récupère le numéro de la facture depuis le formulaire
$supp = isset($_POST['numeroFacture']) ? $_POST['numeroFacture'] : null;

try {

    if ($supp !== null){
        $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		echo "Connexion réussie à la base de données<br>";

        $sql = "SELECT facturation.*
                FROM facturation
                JOIN soins ON facturation.idFacturation = soins.idFacturation
                JOIN realise ON soins.idSoins = realise.idSoins
                JOIN encode e ON realise.idVisite = e.idVisite
                WHERE e.numeroInami = :numeroInami AND facturation.idFacturation = :supp";
        $stmt = $base->prepare($sql);
        $stmt->execute([':numeroInami' => $numeroInami, ':supp' => $supp]);

        // Si la facture appartient à l'infirmière, procéder à la suppression
        if ($stmt->rowCount() > 0) {
            // Dissocier la facture des soins (mettre l'idFacturation à NULL)
            $updateSql = "UPDATE soins SET idFacturation = NULL WHERE idFacturation = :supp";
            $updateStmt = $base->prepare($updateSql);
            $updateStmt->execute([':supp' => $supp]);

            echo "<br>La facture " . $supp . " est dissociée des soins.";
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

    } 
}catch (PDOException $e) {
    echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
    exit;
}
?>
