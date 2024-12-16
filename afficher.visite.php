<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des visites</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
    .w3-btn {width:150px;}
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="w3-container">
        <div class="col">
            <button class="btn btn-primary" style="margin-top: 32px;">
            <a href="home.visite.html" style="color: white; text-decoration: none;">Retour</a>
            </button>
        </div>
        <h1 class="text-center mb-4">Liste des visites</h1>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Numéro de la visite</th>
                    <th>Nom du patient</th>
                    <th>Prénom du patient</th>
                    <th>Date de la visite</th>
                    <th>Brève description du soin</th>
                    <th>Fréquence du soin</th>
                    <th>Heure de la visite</th>
                    <th>Numéro de rapport associé</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody> 

                <?php
                    header('Content-Type: text/html; charset=utf-8');

                    session_start();
                    $numeroInami = $_SESSION['numeroInami'];

                // Traitement de la suppression
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idVisite'])) {
                $visiteASupprimer = $_POST['idVisite'];

                try {
                    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Vérifier si la visite appartient à l'infirmier
                    $checkSql = "SELECT * FROM encode WHERE numeroInami = :numeroInami AND idVisite = :idVisite";
                    $stmtCheck = $base->prepare($checkSql);
                    $stmtCheck->execute([':numeroInami' => $numeroInami, ':idVisite' => $visiteASupprimer]);

                    if ($stmtCheck->rowCount() > 0) {
                        // Suppression autorisée
                        $deleteSql = "DELETE FROM visite WHERE idVisite = :idVisite";
                        $stmtDelete = $base->prepare($deleteSql);
                        $stmtDelete->execute([':idVisite' => $visiteASupprimer]);

                        echo "<script>alert('La visite a été supprimée avec succès.');</script>";
                        header("Refresh:0"); // Rafraîchir la page pour actualiser la liste
                        exit;
                    } else {
                        echo "<script>alert('Erreur : Cette visite ne vous appartient pas ou n\'existe pas.');</script>";
                    }
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                }
            }
                
            $visitesTrouves = false;

        try {
            $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');

            $sql = "SELECT v.*, p.nom AS patientNom, p.prenom AS patientPrenom
                FROM visite v 
                JOIN encode e ON v.IdVisite = e.idVisite
                JOIN patient p ON e.numeroNISS = p.numeroNISS
                LEFT JOIN realise r ON v.IdVisite = r.idVisite  -- Jointure avec la table 'Realise'
                WHERE e.numeroInami = :numeroInami AND v.idVisite != 121";

            $stmt = $base->prepare($sql);
            $stmt->bindParam(":numeroInami", $numeroInami);
            $stmt->execute();
            if ($stmt->rowcount() >0){
                $visitesTrouves = true;
                while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $idVisite = htmlspecialchars($ligne['idVisite']);
                    $patientNom = htmlspecialchars($ligne['patientNom']);
                    $patientPrenom = htmlspecialchars($ligne['patientPrenom']);
                    $date_visite = htmlspecialchars($ligne['dateR']);
                    $description = htmlspecialchars($ligne['description']);
                    $frequence = htmlspecialchars($ligne['frequence']);
                    $heure = htmlspecialchars($ligne['heure']);
            
                    //$idRapport = -1;
                    if (is_null($ligne['idRapport'])){
                        $idRapport = "";
                    }else{
                        $idRapport = htmlspecialchars($ligne['idRapport']);
                    }
                    
                    echo "<tr>";
                    echo "<td>$idVisite</td>";
                    echo "<td>$patientNom</td>";
                    echo "<td>$patientPrenom</td>";
                    echo "<td>$date_visite</td>";
                    echo "<td>$description</td>";
                    echo "<td>$frequence</td>";
                    echo "<td>$heure</td>";
                    echo "<td>$idRapport</td>";   
                    echo "<td>
                                    <form method='POST' style='display:inline;' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer cette visite ?');\">
                                        <input type='hidden' name='idVisite' value='$idVisite'>
                                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                                    </form>
                                </td>";
                            echo "</tr>";
                }
            } 
            }catch (Exception $e) {
                echo "Erreur : " . $e->getMessage();
            }

            if (!$visitesTrouves){
                echo "<tr><td colspan='11' class='text-center'>Aucune visite encodée pour le moment</td></tr>";
            }
            ?>
            <tbody>
        </table>

    </div>   
</body>
</html>