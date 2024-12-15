<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {
	
		$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sqlCheck = "SELECT DISTINCT p.* 
        FROM encode
        JOIN patient p ON encode.numeroNiss = p.numeroNiss
        WHERE encode.numeroInami = :numeroInami";
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
<head>
    <div class="col">
            <button class="btn btn-primary" style="margin-top: 32px;">
            <a href="home.visite.html" style="color: white; text-decoration: none;">Retour</a>
            </button>
        </div>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification d'un patient</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Modifier un Patient</h1>

        <!-- Formulaire pour rechercher un patient par NISS -->
        <form action="" method="GET" class="mb-4">
            <div class="form-group">
                <label for="searchNISS">Entrez le NISS du patient à modifier :</label>
                <input type="text" class="form-control" id="searchNISS" name="searchNISS" placeholder="Numéro NISS" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Rechercher</button>
        </form>

        <?php
        if (isset($_GET['searchNISS']) && !empty($_GET['searchNISS'])) {
            $searchNISS = $_GET['searchNISS']; // variable de recherche 

            try {
                // Connexion à la base de données
                $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Requête pour récupérer les informations du patient
                $stmt = $base->prepare("SELECT * FROM patient WHERE numeroNiss =?");
                $stmt->execute([$searchNISS]);
                $patient = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($patient) {
        ?>

        <!-- Formulaire pour modifier les informations du patient -->
        <form action="modifier.patient.php" method="POST">
            <input type="hidden" name="idmod" value="<?php echo htmlspecialchars($patient['numeroNiss']); ?>">
            

            <div class="form-group">
                <label for="numeroNiss">Numéro NISS</label>
                <input type="text" class="form-control" id="numeroNiss" name="numeroNiss" value="<?php echo htmlspecialchars($patient['numeroNiss']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($patient['nom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($patient['prenom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="dateDeNaissance">Date de Naissance</label>
                <input type="date" class="form-control" id="dateDeNaissance" name="dateDeNaissance" value="<?php echo htmlspecialchars($patient['dateDeNaissance']); ?>" required>
            </div>
            <div class="form-group">
                <label for="rue">Rue</label>
                <input type="text" class="form-control" id="rue" name="rue" value="<?php echo htmlspecialchars($patient['rue']); ?>" required>
            </div>
            <div class="form-group">
                <label for="numeroDomicile">Numéro de Domicile</label>
                <input type="text" class="form-control" id="numeroDomicile" name="numeroDomicile" value="<?php echo htmlspecialchars($patient['numeroDomicile']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" class="form-control" id="ville" name="ville" value="<?php echo htmlspecialchars($patient['ville']); ?>" required>
            </div>
            <div class="form-group">
                <label for="sexe">Sexe</label>
                <select class="form-control" id="sexe" name="sexe">
                    <option value="M" <?php echo $patient['sexe'] == 'M' ? 'selected' : ''; ?>>Masculin</option>
                    <option value="F" <?php echo $patient['sexe'] == 'F' ? 'selected' : ''; ?>>Féminin</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numeroInamiMedecin">Numéro Inami du médecin traitant (optionnel)</label>
                <input type="text" class="form-control" id="numeroInamiMedecin" name="numeroInamiMedecin" value="<?php echo htmlspecialchars($patient['numeroInamiMedecin']); ?>">
            </div>
            <div class="form-group">
                <label for="idAssurabilite">Numéro Assurabilité</label>
                <select class="form-control" id="idAssurabilite" name="idAssurabilite">
                    <option value="">-- Sélectionnez une assurabilité --</option>
                    <?php
                    $stmt = $base->query("SELECT idAssurabilite, organismeAssureur, typeAssurabilite FROM assurabilité");
                    while ($assur = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = $patient['idAssurabilite'] == $assur['idAssurabilite'] ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($assur['idAssurabilite']) . '" ' . $selected . '>' 
                            . htmlspecialchars($assur['organismeAssureur']) . ' - ' 
                            . htmlspecialchars($assur['typeAssurabilite']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Modifier le Patient</button>
        </form>

        <?php
                } else {
                    echo '<div class="alert alert-danger">Aucun patient trouvé avec ce NISS.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Erreur : ' . $e->getMessage() . '</div>';
            }
        }
        ?>
    </div>

</body>
</html>
