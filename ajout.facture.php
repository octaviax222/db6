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
                    patient.numeroInami AS patientNumeroInami,
                    patient.idAssurabilite AS patientIdAssurabilite,

                    encode.numeroNiss AS encodeNumeroNiss,
                    encode.idVisite AS encodeIdVisite,
                    encode.numeroInami AS encodeNumeroInami,
                    
                    visite.dateR AS visiteDateR,
                    visite.idVisite AS visiteIdVisite,

                    realise.idInamiTypeSoin AS realiseIdInamiTypeSoin,
                    realise.idVisite as realiseIdVisite,
                    
                    soins.idInamiTypeSoin AS soinsIdInamiTypeSoin,
                    soins.idFacturation AS soinsIdFacturation,
                    soins.descriptionTypeSoin AS soinsDescriptionTypeSoin,

                    facturation.idFacturation AS facturationIdFacturation

                FROM  
                    encode
                JOIN prestataire ON encode.numeroInami = prestataire.numeroInami
                JOIN patient ON encode.numeroNiss = patient.numeroNiss
                JOIN visite ON encode.idVisite = visite.idVisite
                JOIN realise ON visite.idVisite = realise.idVisite
                JOIN soins ON realise.idInamiTypeSoin = soins.idInamiTypeSoin
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
                        <th>Numero du soin effectué</th>
                        <th>Description du soin effectué</th>
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
                        <td>{$resultat['soinsIdInamiTypeSoin']}</td>
                        <td>{$resultat['soinsDescriptionTypeSoin']}</td>
                      </tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "Numéro de la facturation généré : " . $idFacturation;
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