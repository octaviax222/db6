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
            <form action="modifier.visite.php" method="POST">
                <div class="form-group">
                    <label for="idVisite"> Numéro de la visite à modifier </label>
                    <input type="number" class="form-control" id="idVisite" name="idVisite" placeholder="Numéro de la visite">
                </div>
                <div class="form-group">
                    <label for="date_visite">Date de la visite</label>
                    <input type="date" class="form-control" id="date_visite" name="date_visite" placeholder="Date de la visite" >
                </div>
                <div class="form-group">
                    <label for="frequence">Fréquence du soin</label>
                    <input type="text" class="form-control" id="frequence" name="frequence" placeholder="Fréquence du soin">
                </div>
                <div class="form-group">
                    <label for="description">Brève description</label>
                    <input type="text" class="form-control" id="description" name="description" placeholder="Brève description">
                </div>
                <div class="form-group">
                    <label for="heure">Heure de la visite</label>
                    <input type="time" class="form-control" id="heure" name="heure" placeholder="Heure de la visite">
                </div>

                <div class="form-group">
                    <label for="typeSoins">Description type de soin</label>
                    <select class="form-control" id="typeSoins" name="idInamiTypeSoins">
                    <option value="">-- Sélectionnez un soin --</option>
                        <?php

                        $db = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');

                        $soinquery = "SELECT ts.idInamiTypeSoins, ts.descriptionTypeSoins FROM typeSoins ts";
                        $stmt = $db->query($soinquery);
                        $typeSoins = $stmt->fetchAll(PDO::FETCH_ASSOC); // On stocke les résultats dans un tableau associatif
    
                            foreach ($typeSoins as $soin) {
                            echo '<option value="' . $soin['idInamiTypeSoins'] . '">' . htmlspecialchars($soin['descriptionTypeSoins']) . '</option>';
                            }
                        ?>
                    </select>
                </div>
              
                <button type="submit" class="btn btn-primary btn-block">Modifier le Patient</button>
            </form>
        </div>
    </body>
</html>