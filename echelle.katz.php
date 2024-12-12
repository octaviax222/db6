<?php

try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activer le mode exception pour les erreurs PDO
    echo "Connexion réussie";

    // Récupérer les données POST
    $se_laver = $_POST['se_laver'] ?? '';
    $s_habiller = $_POST['s_habiller'] ?? '';
    $transfert = $_POST['transfert'] ?? '';
    $toilette = $_POST['toilette'] ?? '';
    $continence = $_POST['continence'] ?? '';
    $manger = $_POST['manger'] ?? '';
    
    echo "<h3>Résumé des choix :</h3>";
    echo "Se laver : $se_laver<br>";
    echo "S'habiller : $s_habiller<br>";
    echo "Transfert : $transfert<br>";
    echo "Toilette : $toilette<br>";
    echo "Continence : $continence<br>";
    echo "Manger : $manger<br>";

    // Concaténer les différentes valeurs
    $concatener = $se_laver . $s_habiller . $transfert . $toilette . $continence . $manger;

     // Calcul de la moyenne des chiffres
     $valeurs = [$se_laver, $s_habiller, $transfert, $toilette, $continence, $manger];
     $somme = array_sum($valeurs);
     $moyenne = $somme / count($valeurs);
 
     // Déterminer le forfait en fonction de la moyenne
     if($moyenne <= 2){
         $forfait = "Forfait A";
     } elseif ($moyenne <= 4) {
         $forfait = "Forfait B";
     } else {
         $forfait = "Forfait C";
     }
 
    // Récupérer l'ID de la dernière ligne insérée dans la table soins
    $sqlGetLastSoins = "SELECT idSoins FROM soins ORDER BY idSoins DESC LIMIT 1";
    $stmtGetLastSoins = $base->query($sqlGetLastSoins);
    $lastSoins = $stmtGetLastSoins->fetch(PDO::FETCH_ASSOC);

    if ($lastSoins) {
        $idSoins = $lastSoins['idSoins']; // Dernier ID de la table soins
        echo "Dernier soin trouvé, ID : $idSoins<br>";

        // Insérer dans la table toilette
        $sqlToilette = "INSERT INTO toilette (scoreKatz,forfait) VALUES (:concatener,:forfait)";
        $stmtToilette = $base->prepare($sqlToilette);
        $stmtToilette->bindParam(':concatener', $concatener);
        $stmtToilette->bindParam(':forfait', $forfait);

        if ($stmtToilette->execute()) {
            // Récupérer le dernier ID inséré dans la table toilette
            $idToilette = $base->lastInsertId();
            echo "Toilette ajoutée avec succès, ID : $idToilette<br>";

            // Mettre à jour la table soins avec l'ID toilette
            $sqlUpdateSoins = "UPDATE soins SET idToilette = :idToilette WHERE idSoins = :idSoins";
            $stmtUpdateSoins = $base->prepare($sqlUpdateSoins);
            $stmtUpdateSoins->bindParam(':idToilette', $idToilette);
            $stmtUpdateSoins->bindParam(':idSoins', $idSoins);

            if ($stmtUpdateSoins->execute()) {
                echo "Mise à jour réussie : ID toilette associé au soin.<br>";
                echo "<script>
                    alert('Données mises à jour avec succès !');
                    window.location.href = 'home.visite.html'; // Redirige vers la page spécifique
                  </script>";
            } else {
                echo "Erreur lors de la mise à jour de la table soins.<br>";
            }
        } else {
            echo "Erreur lors de l'insertion dans la table toilette.<br>";
        }
    } else {
        echo "Aucune ligne trouvée dans la table soins pour mise à jour.<br>";
    }

        
        
}
catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

