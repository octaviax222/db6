<?php

session_start();

$date_visite = $_POST['date_visite'];
$description = $_POST['description'];
$frequence = $_POST['frequence'];
$heure = $_POST['heure'];
$patients = $_POST['patients']; //récupérer le patient sélectionné
$idInamiTypeSoin = $_POST['idInamiTypeSoin'];

$numeroInami = $_SESSION['numeroInami'];

echo "Date de la visite : ".$date_visite;
echo "<br>";
echo "Fréquence du soin (optionnel) : ".$frequence;
echo "<br>";
echo "Brève description du soin (optionnel) : ".$description;
echo "<br>";
echo "Heure de la visite (optionnel) : ".$heure;
echo "<br>";
echo "ID du soin sélectionné : " . $idInamiTypeSoin;
echo "<br>";

try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6', 'user6');
    echo "Connexion réussie à la base de données<br>";

    // Insertion des données sans l'ID auto-incrémenté
    $sql = "INSERT INTO visite(dateR, frequence, description, heure, idRapport)
            VALUES ('$date_visite', '$frequence', '$description', '$heure', " . 
        (isset($idRapport) ? $idRapport : "NULL") . ")";
    $Resultat = $base->exec($sql);
    $idVisite = $base->lastInsertId();
    echo "L'ID de la visite inséré est : ".$idVisite;


    //mise a jour de l'id visite dans encode:
    if(!empty($patients))
    {
        foreach ($patients as $numeroNiss) {
            $stmt = $base->prepare("INSERT INTO encode (numeroInami, idVisite, numeroNiss) VALUES (:numeroInami,:idVisite,:numeroNiss)");
            $stmt->bindParam(':numeroInami',$numeroInami);
            $stmt->bindParam(':idVisite',$idVisite);
            $stmt->bindParam(':numeroNiss',$numeroNiss);
            $stmt->execute();
            echo "ok";
        }
    }

    if(!empty($idInamiTypeSoin))
    {
            $stmt = $base->prepare("INSERT INTO realise (idInamiTypeSoin,idVisite) VALUES (:idInamiTypeSoin, :idVisite)");
            $stmt->bindParam(':idInamiTypeSoin',$idInamiTypeSoin);
            $stmt->bindParam(':idVisite',$idVisite);
            $stmt->execute();
            echo "ok";
    }

    // Vérification de l'insertion et récupération du dernier ID inséré
    if ($Resultat) {
        $idVisite = $base->lastInsertId(); // Récupère l'ID de la visite générée automatiquement
        echo "Numéro de la visite (généré automatiquement) : " . $idVisite;
        if ($idInamiTypeSoin ==425110) {
        
            // Si le type de soin est "toilette", redirige vers la page Katz
            header("Location: page.echelle.katz.html");
        } else {
            // Sinon, redirige vers la page d'accueil
            header("Location: home.visite.html");
        }
        exit(); // Assurez-vous d'utiliser exit après header pour stopper l'exécution du script
    } else {
        echo "Erreur lors de l'insertion de la visite.";
    }
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>