<?php

session_start();
$numeroInami = $_SESSION['numeroInami'];

try {
   // Connexion à la base de données
   $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6', 'user6');
   $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   // Récupérer les dates du formulaire
   if (isset($_POST['dateDebut']) && isset($_POST['dateFin'])) {
       $dateDebut = $_POST['dateDebut'];
       $dateFin = $_POST['dateFin'];

       // Vérifier si les dates sont valides
       if (!empty($dateDebut) && !empty($dateFin)) {
           // Requête SQL avec BETWEEN
           $query = $base->prepare("
               SELECT 
                   prestataire.numeroInami AS prestataireNumeroInami,
                   prestataire.nom AS prestataireNom,
                   prestataire.prenom AS prestatairePrenom,

                   patient.numeroNiss AS patientNumeroNiss,
                   patient.nom AS patientNom,
                   patient.prenom AS patientPrenom,
                   patient.idAssurabilite AS patientIdAssurabilite,

                   encode.numeroNiss AS encodeNumeroNiss,
                   encode.idVisite AS encodeIdVisite,
                   encode.numeroInami AS encodeNumeroInami,
                   
                   visite.dateR AS visiteDateR,
                   visite.idVisite AS visiteIdVisite,

                   realise.idSoins AS realiseIdsoins,
                   realise.idVisite as realiseIdVisite,
                   
                   soins.idSoins AS soinsIdSoins,
                   soins.idFacturation AS soinsIdFacturation,

                   toilette.idToilette AS toiletteIdToilette,
                   toilette.forfait AS toiletteForfait,

                   typeSoins.idInamiTypeSoins AS typeSoinsIdInamiTypeSoins,
                   typeSoins.descriptionTypeSoins AS typesSoinsDescriptionTypeSoins,
                   
                   facturation.idFacturation AS facturationIdFacturation

               FROM  
                   encode
               JOIN prestataire ON encode.numeroInami = prestataire.numeroInami
               JOIN patient ON encode.numeroNiss = patient.numeroNiss
               JOIN visite ON encode.idVisite = visite.idVisite
               JOIN realise ON visite.idVisite = realise.idVisite
               JOIN soins ON realise.idSoins = soins.idSoins
               JOIN typeSoins ON soins.idInamiTypeSoins = typeSoins.idInamiTypeSoins
               JOIN toilette ON soins.idToilette = toilette.idToilette
               JOIN facturation ON soins.idFacturation = facturation.idFacturation

               WHERE 
                   encode.numeroInami = :numeroInami AND
                   visite.dateR BETWEEN :dateDebut AND :dateFin;
           ");

           // Exécution de la requête
           $query->execute([
               ':numeroInami' => $numeroInami,
               ':dateDebut' => $dateDebut,
               ':dateFin' => $dateFin,
           ]);

           // Récupérer les résultats
           $resultats = $query->fetchAll(PDO::FETCH_ASSOC);
           $insertFacture = $base->prepare("INSERT INTO facturation (dateFacturation) VALUES (NOW())");
           $insertFacture->execute();
           
           $idFacturation = $base->lastInsertId();

           // Récupérer tous les idSoins à mettre à jour
           $querySoins = $base->prepare("
           SELECT soins.idSoins
           FROM encode
           JOIN visite ON encode.idVisite = visite.idVisite
           JOIN realise ON visite.idVisite = realise.idVisite
           JOIN soins ON realise.idSoins = soins.idSoins
           WHERE encode.numeroInami = :numeroInami AND visite.dateR BETWEEN :dateDebut AND :dateFin
           ");
           $querySoins->execute([
           ':numeroInami' => $numeroInami,
           ':dateDebut' => $dateDebut,
           ':dateFin' => $dateFin,
           ]);

           $soins = $querySoins->fetchAll(PDO::FETCH_ASSOC);

           // Mettre à jour chaque soin individuellement
           $updateSoins = $base->prepare("UPDATE soins SET idFacturation = :idFacturation WHERE idSoins = :idSoins");
           foreach ($soins as $soin) {
           $updateSoins->execute([
               ':idFacturation' => $idFacturation,
               ':idSoins' => $soin['idSoins'],
           ]);
           }

           // Afficher les résultats
           echo "<h2>Factures de la période $dateDebut à $dateFin</h2>";
           echo "<table class='table'>";
           echo "<thead><tr>
                       <th>Nom du prestataire</th>
                       <th>Prenom du prestataire</th>
                       <th>numero NISS du patient</th>
                       <th>Nom du patient</th>
                       <th>Prenom du patient</th>
                       <th>Date de la visite du patient</th>
                       <th>INAMI du soin </th>
                       <th>Forfait du patient</th>
                   </tr></thead>";
           echo "<tbody>";
           foreach ($resultats as $resultat) {
               echo "<tr>
                       <td>{$resultat['prestataireNom']}</td>
                       <td>{$resultat['prestatairePrenom']}</td>
                       <td>{$resultat['patientNumeroNiss']}</td>
                       <td>{$resultat['patientNom']}</td>
                       <td>{$resultat['patientPrenom']}</td>
                       <td>{$resultat['visiteDateR']}</td>
                       <td>{$resultat['typeSoinsIdInamiTypeSoins']}</td>
                       <td>{$resultat['toiletteForfait']}</td>
                     </tr>";
           }
           echo "</tbody>";
           echo "</table>";
           echo " Numéro de la facturation générée : " . $idFacturation ;
           echo "<br> Facturation créée !</br>";
       } else {
           echo "Veuillez sélectionner une période valide.";
       }
   } else {
       echo "Erreur : données manquantes.";
   }
} catch (PDOException $e) {
   echo "Erreur de connexion : " . $e->getMessage();
}
?>