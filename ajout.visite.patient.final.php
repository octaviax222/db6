<hmtl>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ajouter une visite</title> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>


            <form action="ajout.visite.final.php" method="POST">

                <div class="container mt-5">
                <div class="col">
                    <button class="btn btn-primary" style="margin-top: 32px;">
                    <a href="home.visite.html" style="color: white; text-decoration: none;">Retour</a>
                    </button>
                </div>
                <h1 class="text-center mb-4">Ajouter une visite</h1>
                <label>Sélectionnez les patients :</label><br>
                    <?php
                    
                    session_start();

                    // Connexion à la base de données
                    $db = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                
                    $numeroInami = $_SESSION['numeroInami'];

                       // Prépare la requête pour récupérer uniquement les patients associés à l'infirmière connectée
                        $query = $db->prepare("SELECT DISTINCT p.numeroNiss, p.nom, p.prenom
                            FROM patient p
                            JOIN encode e ON p.numeroNiss = e.numeroNiss
                            WHERE e.numeroInami = :numeroInami
                    ");
                    $query->bindParam(':numeroInami', $numeroInami);
                    $query->execute();

                    // Récupérer les descriptions de soins et leurs ID depuis la table `soins`
                    $soinquery = "SELECT ts.idInamiTypeSoins, ts.descriptionTypeSoins FROM typeSoins ts";
                    
                    $stmt = $db->query($soinquery);
                    $typeSoins = $stmt->fetchAll(PDO::FETCH_ASSOC); // On stocke les résultats dans un tableau associatif


                    // Récupère les visites sans rapport associé
                    //$query = $db->query("SELECT numeroNiss, nom, prenom FROM patient");
                        // atttentionnn afficherrr patient prestataire
                    while ($patient = $query->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div>';
                        echo '<input type="checkbox" name="patients[]" value="' . $patient['numeroNiss'] . '"> ';
                        echo 'Patient ' . $patient['numeroNiss'] . ' - ' . $patient['nom'] . ' ' . $patient['prenom'];
                        echo '</div>';
                    }
                    
                    ?>
                  
                <div class="form-group">
                    <label for="date_visite">Date de la visite</label>
                    <input type="date" class="form-control" id="date_visite" name="date_visite" placeholder="Date de la visite" required>
                </div>

                <div class="form-group">
                    <label for="frequence">Fréquence du soin (optionnel)</label>
                    <input type="text" class="form-control" id="frequence" name="frequence" placeholder="Fréquence du soin (optionnel)">
                </div>

                <div class="form-group">
                    <label for="description">Brève description du soin (optionnel)</label>
                    <input type="text" class="form-control" id="description" name="description" placeholder="Brève description du soin (optionnel)">
                </div>

                <div class="form-group">
                    <label for="heure">Heure de la visite (optionnel)</label>
                    <input type="time" class="form-control" id="heure" name="heure" placeholder="Heure de la visite (optionnel)">
                </div>

                <div class="form-group">
                    <label for="typeSoins">Description type de soin</label>
                    <select class="form-control" id="typeSoins" name="idInamiTypeSoins" required>
                    <option value="">-- Sélectionnez un soin --</option>
                        <?php
                            foreach ($typeSoins as $soin) {
                            echo '<option value="' . $soin['idInamiTypeSoins'] . '">' . htmlspecialchars($soin['descriptionTypeSoins']) . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Ajouter la visite</button>
            </form>
        </div>
    </body>
</hmtl>