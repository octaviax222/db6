<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {
	
		$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		

		$sqlCheck = "SELECT DISTINCT v.* 
        FROM visite v 
        JOIN encode e ON v.idVisite = e.idVisite
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
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <div class="col">
            <button class="btn btn-primary" style="margin-top: 32px;">
            <a href="home.visite.html" style="color: white; text-decoration: none;">Retour</a>
            </button>
        </div>
        <title>Modification d'une visite</title> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        <div class="container mt-5">
            <h1 class="text-center mb-4">Modifier une visite</h1>
            <!-- Formulaire pour rechercher une visite par ID -->
            <form action="" method="GET" class="mb-4">
                <div class="form-group">
                    <label for="searchVisite">Entrez l'ID de la visite :</label>
                    <input type="text" class="form-control" id="searchVisite" name="searchVisite" placeholder="ID de la visite" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Rechercher</button>
            </form>

            <?php
            if (isset($_GET['searchVisite']) && !empty($_GET['searchVisite'])) {
                $searchVisite = $_GET['searchVisite']; // Récupérer l'ID de la visite depuis le formulaire

                try {
                    // Connexion à la base de données
                    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Requête pour récupérer les informations de la visite
                    $stmt = $base->prepare("SELECT * FROM visite WHERE idVisite = ?");
                    $stmt->execute([$searchVisite]);
                    $visite = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($visite) {
            ?>
            <!-- Formulaire pour modifier la visite -->
            <form action="modifier.visite.php" method="POST">
                <div class="form-group">
                    <label for="idVisite">Numéro de la visite à modifier</label>
                    <input type="number" class="form-control" id="idVisite" name="idVisite" value="<?= htmlspecialchars($visite['idVisite']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="date_visite">Date de la visite</label>
                    <input type="date" class="form-control" id="date_visite" name="date_visite" value="<?= htmlspecialchars($visite['dateR']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="frequence">Fréquence du soin</label>
                    <input type="text" class="form-control" id="frequence" name="frequence" value="<?= htmlspecialchars($visite['frequence']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Brève description</label>
                    <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($visite['description']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="heure">Heure de la visite</label>
                    <input type="time" class="form-control" id="heure" name="heure" value="<?= htmlspecialchars($visite['heure']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="typeSoins">Description type de soin</label>
                    <select class="form-control" id="typeSoins" name="idInamiTypeSoins" required>
                        <option value="">-- Sélectionnez un soin --</option>
                        <?php
                        // Récupérer les types de soins
                        $soinquery = "SELECT ts.idInamiTypeSoins, ts.descriptionTypeSoins FROM typeSoins ts";
                        $stmt = $base->query($soinquery);
                        $typeSoins = $stmt->fetchAll(PDO::FETCH_ASSOC); // On stocke les résultats dans un tableau associatif

                        foreach ($typeSoins as $soin) {
                            $selected = ($soin['idInamiTypeSoins'] == $visite['idInamiTypeSoins']) ? 'selected' : '';
                            echo '<option value="' . $soin['idInamiTypeSoins'] . '" ' . $selected . '>' . htmlspecialchars($soin['descriptionTypeSoins']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Modifier la visite</button>
            </form>
            <?php
                    } else {
                        echo "<p class='alert alert-danger'>Aucune visite trouvée avec cet ID.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='alert alert-danger'>Erreur : " . $e->getMessage() . "</p>";
                }
            }
            ?>
        </div>
    </body>
</html>
