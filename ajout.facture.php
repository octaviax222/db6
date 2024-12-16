<?php
session_start();
$numeroInami = $_SESSION['numeroInami'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connexion à la base de données
        $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les dates du formulaire
        if (isset($_POST['dateDebut']) && isset($_POST['dateFin'])) {
            $dateDebut = $_POST['dateDebut'];
            $dateFin = $_POST['dateFin'];

            if (!empty($dateDebut) && !empty($dateFin)) {
                // Insérer une nouvelle facturation
                $insertFacture = $base->prepare("INSERT INTO facturation (dateFacturation) VALUES (NOW())");
                $insertFacture->execute();
                $idFacturation = $base->lastInsertId();

                // Mettre à jour les soins associés à la période
                $querySoins = $base->prepare("
                    SELECT soins.idSoins
                    FROM encode
                    JOIN visite ON encode.idVisite = visite.idVisite
                    JOIN realise ON visite.idVisite = realise.idVisite
                    JOIN soins ON realise.idSoins = soins.idSoins
                    WHERE encode.numeroInami = :numeroInami 
                    AND visite.dateR BETWEEN :dateDebut AND :dateFin
                ");
                $querySoins->execute([
                    ':numeroInami' => $numeroInami,
                    ':dateDebut' => $dateDebut,
                    ':dateFin' => $dateFin,
                ]);

                $soins = $querySoins->fetchAll(PDO::FETCH_ASSOC);
                $updateSoins = $base->prepare("UPDATE soins SET idFacturation = :idFacturation WHERE idSoins = :idSoins");

                foreach ($soins as $soin) {
                    $updateSoins->execute([
                        ':idFacturation' => $idFacturation,
                        ':idSoins' => $soin['idSoins'],
                    ]);
                }

                // Redirection après ajout de la facture
                echo "<script>alert('Facture ajoutée avec succès !'); window.location.href='home.facturation.html';</script>";
                exit;
            } else {
                echo "<script>alert('Veuillez renseigner des dates valides.'); window.history.back();</script>";
                exit;
            }
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erreur : " . htmlspecialchars($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter ses factures</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" 
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="col">
            <button class="btn btn-primary" style="margin-top: 32px;">
                <a href="home.facturation.html" style="color: white; text-decoration: none;">Retour</a>
            </button>
        </div>

        <h1 class="text-center mb-4">Ajouter ses factures</h1>
        <form action="ajout.facture.php" method="POST">
            <div class="form-group">
                <label for="dateDebut">Date de début de la période à facturer</label>
                <input type="date" class="form-control" id="dateDebut" name="dateDebut" placeholder="Date de début de la période" required>
            </div>
            <div class="form-group">
                <label for="dateFin">Date de fin de la période à facturer</label>
                <input type="date" class="form-control" id="dateFin" name="dateFin" placeholder="Date de fin de la période" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Créer la facture</button>
        </form>
    </div>
</body>
</html>
