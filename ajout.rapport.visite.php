<html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un rapport</title> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Ajout d'un rapport</h1>
        <h2>Ajouter un rapport</h2>
        
        <form action="ajout.rapport.php" method="post">
            <div class="form-group">
                  <label>Sélectionnez les visites à inclure dans le rapport :</label><br>
                  <?php
                  session_start(); // on se connecte à la session afin d'avoir les informations 
                  // Connexion à la base de données
                  $db = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
                  
                  // Récupère les visites sans rapport associé
                  //$query = $db->query("SELECT idVisite, dateR, description, heure FROM visite WHERE idRapport is NULL");
                  $numeroInami = $_SESSION['numeroInami'];

                  // Prépare la requête pour récupérer uniquement les patients associés à l'infirmière connectée
                   $query = $db->prepare(
                    "SELECT v.idVisite, v.dateR, v.description, v.heure
                    FROM visite v
                    JOIN encode e ON v.idVisite = e.idVisite
                    WHERE e.NumeroInami = :numeroInami
                    AND v.idRapport IS NULL 
                    AND v.idVisite !=52 ");
                    $query->bindParam(':numeroInami', $numeroInami);
                    $query->execute();

                  while ($visite = $query->fetch(PDO::FETCH_ASSOC)) {
                      echo '<div>';
                      echo '<input type="checkbox" name="visites[]" value="' . $visite['idVisite'] . '"> ';// permet de donner l'option pour cocher 
                      echo 'Visite ' . $visite['idVisite'] . ' - ' . $visite['dateR'] . ' - ' . $visite['description'];
                      echo '</div>';
                  }
                  ?>
              </div>
            <!-- Évaluation du traitement -->
            <div class="form-group">
                <label for="evaluationTraitement">Évaluation du traitement</label>
                <textarea class="form-control" id="evaluationTraitement" name="evaluationTraitement" rows="3" placeholder="Entrez l'évaluation du traitement" required></textarea>
            </div>
            
            <!-- Observation clinique -->
            <div class="form-group">
                <label for="observationClinique">Observation clinique</label>
                <textarea class="form-control" id="observationClinique" name="observationClinique" rows="3" placeholder="Entrez l'observation clinique" required></textarea>
            </div>
            
            <!-- Bouton de soumission -->
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>






