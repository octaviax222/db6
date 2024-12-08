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

    // Préparer la requête SQL
    $sql = "INSERT INTO toilette (scoreKatz) VALUES (:concatener)";
    $stmt = $base->prepare($sql);
    // Exécuter la requête
    $stmt->bindParam(':concatener' , $concatener);

    if ($stmt->execute()) {
        // Récupérer le dernier ID inséré dans la table toilette
        $idToilette = $base->lastInsertId();
        echo "Toilette ajoutée avec succès, ID : $idToilette<br>";

        // Insérer dans la table soins avec l'ID de toilette
        $descriptionTypeSoin = "Soins d'hygiène (toilettes)";
        $sqlSoins = "INSERT INTO soins (idInamiTypeSoin,descriptionTypeSoin,idToilette,idFacturation) VALUES (:idInamiTypeSoin,:descriptionTypeSoin,:idToilette,:idFacturation)";
        $stmtSoins = $base->prepare($sqlSoins);
        $idInamiTypeSoin = '425110';
        $idfacturation=1;
        $stmtSoins->bindParam(':idInamiTypeSoin', $idInamiTypeSoin );
        $stmtSoins->bindParam(':descriptionTypeSoin', $descriptionTypeSoin);
        $stmtSoins->bindParam(':idToilette', $idToilette);
        $stmtSoins->bindParam(':idFacturation',$idfacturation);

        if ($stmtSoins->execute()) {
            echo "Soin ajouté avec succès pour la toilette.<br>";
            echo "<script>
                alert('Données ajoutées avec succès !');
                window.location.href = 'home.visite.html'; // Redirige vers la page spécifique
              </script>";
        } else {
            echo "Erreur lors de l'ajout dans la table soins.<br>";
        }
    } else {
        echo "Erreur lors de l'ajout dans la table toilette.<br>";
    }
}
catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}