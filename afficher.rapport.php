<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Liste des Rapports</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        .w3-btn { width: 150px; }
    </style>
</head>
<body>
    <div class="w3-container">
        <div class="col">
            <button class="btn btn-primary" style="margin-top: 32px;">
                <a href="home.rapport.html" style="color: white; text-decoration: none;">Retour</a>
            </button>
        </div>
        <h1 class="text-center mb-4">Liste des Rapports</h1>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID Rapport</th>
                    <th>nom patient</th>
                    <th>prenom </th>
                    <th>Évaluation Traitement</th>
                    <th>Observation Clinique</th>
                    
                    <th>Action</th>
                </tr>
            </thead>
            <tbody >
                <?php
                    header('Content-Type: text/html; charset=utf-8');
                    session_start();
                    $numeroInami = $_SESSION['numeroInami']; // prise de l'identifiant de l'infirmière

                    // Traitement de la suppression
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idRapport'])) {
                        $rapportASupprimer = $_POST['idRapport'];

                        try {
                            $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                            $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            // Vérifier si le patient appartient à l'infirmier
                            $checkSql = "SELECT rapportpatient.*
                                            FROM rapportpatient 
                                            JOIN visite v ON rapportpatient.idRapport = v.idRapport
                                            JOIN encode e ON v.idVisite = e.idVisite
                                            WHERE e.NumeroInami = :numeroInami AND rapportpatient.idRapport = :idRapport";
                            $stmtCheck = $base->prepare($checkSql);
                            $stmtCheck->execute([':numeroInami' => $numeroInami, ':idRapport' => $rapportASupprimer]);

                            if ($stmtCheck->rowCount() > 0) {
                                // Remettre à NULL les idRapport des visites liées
                                $resetStmt = $base->prepare("UPDATE visite SET idRapport = NULL WHERE idRapport = :idRapport");
                                $resetStmt->bindParam(':idRapport', $rapportASupprimer);
                                $resetStmt->execute();
                                // Suppression autorisée
                                $deleteSql = "DELETE FROM rapportpatient WHERE idRapport = :idRapport";
                                $stmtDelete = $base->prepare($deleteSql);
                                $stmtDelete->execute([':idRapport' => $rapportASupprimer]);

                                echo "<script>alert('Le rapport a été supprimé avec succès.');</script>";
                                header("Refresh:0"); // Rafraîchir la page pour actualiser la liste
                                exit;
                            } else {
                                echo "<script>alert('Erreur : Ce rapport ne vous appartient pas ou n\'existe pas.');</script>";
                            }
                        } catch (PDOException $e) {
                            echo "Erreur : " . $e->getMessage();
                        }
                    }

                        $rapportsTrouves = false;

                        try {
                            $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                        
                            $sql = "SELECT rapportpatient.*, patient.nom ,patient.prenom
                            FROM rapportpatient 
                            JOIN visite v ON rapportpatient.IdRapport = v.IdRapport -- Jointure entre 'rapportpatient' et 'visite'
                            JOIN encode e ON v.IdVisite = e.IdVisite -- Jointure avec la table 'encode'
                            JOIN patient ON e.NumeroNiss = patient.NumeroNiss -- Jointure pour récupérer le nom du patient
                            JOIN prestataire p ON e.NumeroInami = p.NumeroInami -- Jointure avec 'prestataire'
                            LEFT JOIN realise r ON v.IdVisite = r.IdVisite -- Jointure facultative avec 'realise'
                            WHERE e.NumeroInami = :numeroInami;";
                            
                            
                            $stmt = $base->prepare($sql);
                            $stmt->bindParam(":numeroInami", $numeroInami);
                            $stmt->execute();

                            if($stmt->rowcount()>0){

                                $rapportsTrouves = true;
                                echo "Le nombre de rapports dans la base de données est : <strong>" . $stmt->rowCount() . "</strong><br><br>";
                                $rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($rapports as $ligne) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($ligne['idRapport']) . "</td>";
                                    echo "<td>" . htmlspecialchars($ligne['nom']) . "</td>"; // Affiche le nom du patient
                                    echo "<td>" . htmlspecialchars($ligne['prenom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($ligne['evaluationTraitement']) . "</td>";
                                    echo "<td>" . htmlspecialchars($ligne['observationClinique']) . "</td>";
                                    
                                    echo "<td>
                                    <form method='POST' style='display:inline;' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?');\">
                                        <input type='hidden' name='idRapport' value='" . htmlspecialchars($ligne['idRapport']) . "']'>
                                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                                    </form>
                                    </td>";
                                    echo "</tr>";
                                }

                            }
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
                        }
                        if (!$rapportsTrouves) {
                            echo "<tr><td colspan='11' class='text-center'>Aucun rapport encodé pour le moment</td></tr>";
                        }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
