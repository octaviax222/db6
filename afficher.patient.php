<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <div class="col">
        <button class="btn btn-primary" style="margin-top: 32px;">
            <a href="home.patient.html" style="color: white; text-decoration: none;">Retour</a>
        </button>
    </div>

    <title>Liste des Patients</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
    .w3-btn {width:150px;}
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="w3-container">
        <h1 class="text-center mb-4">Liste des Patients</h1>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Numéro NISS</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date de Naissance</th>
                    <th>Rue</th>
                    <th>Numéro Domicile</th>
                    <th>Ville</th>
                    <th>Sexe</th>
                    <th>Nom du médecin traitant</th>
                    <th>Prenom du médecin traitant</th>
                    <th>Assurabilité</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <?php
                header('Content-Type: text/html; charset=utf-8');
                session_start();
                $numeroInami = $_SESSION['numeroInami'];

            // Traitement de la suppression
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numeroNiss'])) {
                $nissASupprimer = $_POST['numeroNiss'];

                try {
                    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Vérifier si le patient appartient à l'infirmier
                    $checkSql = "SELECT * FROM encode WHERE numeroInami = :numeroInami AND numeroNiss = :numeroNiss";
                    $stmtCheck = $base->prepare($checkSql);
                    $stmtCheck->execute([':numeroInami' => $numeroInami, ':numeroNiss' => $nissASupprimer]);

                    if ($stmtCheck->rowCount() > 0) {
                        // Suppression autorisée
                        $deleteSql = "DELETE FROM patient WHERE numeroNiss = :numeroNiss";
                        $stmtDelete = $base->prepare($deleteSql);
                        $stmtDelete->execute([':numeroNiss' => $nissASupprimer]);

                        echo "<script>alert('Le patient a été supprimé avec succès.');</script>";
                        header("Refresh:0"); // Rafraîchir la page pour actualiser la liste
                        exit;
                    } else {
                        echo "<script>alert('Erreur : Ce patient ne vous appartient pas ou n\'existe pas.');</script>";
                    }
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                }
            }

                $patientsTrouves = false;

                try {
                    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                    $sql = "SELECT DISTINCT
                            p.numeroNiss, 
                            p.nom, 
                            p.prenom, 
                            p.dateDeNaissance, 
                            p.rue, 
                            p.numeroDomicile, 
                            p.ville, 
                            p.sexe, 
                            p.numeroInamiMedecin, 
                            a.organismeAssureur, 
                            a.typeAssurabilite,
                            mt.numeroInamiMedecin,
                            mt.Nom,
                            mt.Prenom
                        FROM 
                            patient p
                        LEFT JOIN 
                            assurabilité a 
                        ON 
                            p.idAssurabilite = a.idAssurabilite

                        JOIN encode e ON p.numeroNiss = e.numeroNiss
                        JOIN medecintraitant mt ON p.numeroInamiMedecin = mt.numeroInamiMedecin 
                        WHERE e.numeroInami = :numeroInami";


                    $stmt = $base->prepare($sql);
                    $stmt->bindParam(':numeroInami', $numeroInami, PDO::PARAM_STR);
                    $stmt->execute();
                    if ($stmt->rowcount() >0){
                        $patientsTrouves = true; 
                        while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            
                            $niss = htmlspecialchars($ligne['numeroNiss']);
                            $nom = htmlspecialchars($ligne['nom']);
                            $prenom = htmlspecialchars($ligne['prenom']);
                            $dateNaissance = htmlspecialchars($ligne['dateDeNaissance']);
                            $rue = htmlspecialchars($ligne['rue']);
                            $num = htmlspecialchars($ligne['numeroDomicile']);
                            $ville = htmlspecialchars($ligne['ville']);
                            $sexe = htmlspecialchars($ligne['sexe']);
                            $nomMedecin = isset($ligne['Nom']) ? htmlspecialchars($ligne['Nom']) : "";
                            $prenomMedecin = isset($ligne['Prenom']) ? htmlspecialchars($ligne['Prenom']) : "";
                            $assureur = htmlspecialchars($ligne['organismeAssureur']);
                            $typeAssurabilite = htmlspecialchars($ligne['typeAssurabilite']);
                    
                            echo "<tr>";
                            echo "<td>$niss</td>";
                            echo "<td>$nom</td>";
                            echo "<td>$prenom</td>";
                            echo "<td>$dateNaissance</td>";
                            echo "<td>$rue</td>";
                            echo "<td>$num</td>";
                            echo "<td>$ville</td>";
                            echo "<td>$sexe</td>";
                            echo "<td>$nomMedecin</td>";
                            echo "<td>$prenomMedecin</td>";
                            echo "<td>$assureur - $typeAssurabilite</td>";
                            echo "<td>
                                    <form method='POST' style='display:inline;' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?');\">
                                        <input type='hidden' name='numeroNiss' value='$niss'>
                                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                                    </form>
                                </td>";
                            echo "</tr>";
                        }
                    }
                    
                } catch (Exception $e) {
                    echo "Erreur : " . $e->getMessage();
                }
                if (!$patientsTrouves) {
                    echo "<tr><td colspan='11' class='text-center'>Aucun patient encodé pour le moment</td></tr>";
                }
                ?>
            </tbody>
        </table>
    
    </div> 
</body>
</html>