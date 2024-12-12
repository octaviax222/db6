<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ajout d'un patient</title> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        <div class="container mt-5">
            <h1 class="text-center mb-4">Ajouter un Patient</h1>
            <form action="ajout.patient.php" method="POST">
                <div class="form-group">
                    <label for="numeroNISS">Numéro NISS</label>
                    <input type="text" class="form-control" id="numeroNISS" name="numeroNISS" placeholder="Numéro NISS" required>
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                </div>
                <div class="form-group">
                    <label for="dateDeNaissance">Date de Naissance</label>
                    <input type="date" class="form-control" id="dateDeNaissance" name="dateDeNaissance" required>
                </div>
                <div class="form-group">
                    <label for="rue">Rue</label>
                    <input type="text" class="form-control" id="rue" name="rue" placeholder="Rue" required>
                </div>
                <div class="form-group">
                    <label for="numeroDomicile">Numéro de Domicile</label>
                    <input type="text" class="form-control" id="numeroDomicile" name="numeroDomicile" placeholder="Numéro de Domicile" required>
                </div>
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" class="form-control" id="ville" name="ville" placeholder="Ville" required>
                </div>
                <div class="form-group">
                    <label for="sexe">Sexe</label>
                    <select class="form-control" id="sexe" name="sexe" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="numeroInami">Numéro Inami du médecin traitant (optionnel)</label>
                    <input type="text" class="form-control" id="numeroInamiMedecin" name="numeroInamiMedecin" placeholder="Numéro Inami">
                </div>
            
                <div class="form-group">
                    <label for="idAssurabilite">Numéro Assurabilite</label>
                    <select class="form-control" id="idAssurabilite" name="idAssurabilite" required>
                    <option value="">-- Sélectionnez une assurabilité --</option>
                        <?php
                        try{
                            $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                            $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                            $sql = "SELECT idAssurabilite, organismeAssureur, typeAssurabilite FROM assurabilité";
                            $stmt = $base->query($sql);

                            // Récupération des données dans un tableau associatif
                            $assurabilite = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($assurabilite as $assur) {
                            echo '<option value="' . htmlspecialchars($assur['idAssurabilite'])  . '">' 
                            . htmlspecialchars($assur['organismeAssureur']) 
                            . ' - '
                            . htmlspecialchars($assur['typeAssurabilite']) 
                            . '</option>';
                            }
                        } catch (PDOException $e) {
                            echo "Erreur : " . $e->getMessage();
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Ajouter le Patient</button>
            </form>
        </div>
    </body>

</html>