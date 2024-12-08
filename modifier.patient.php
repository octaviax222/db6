<?php
try {

    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    echo "Connexion à la base de données réussie<br>";

    $idmod = $_POST['idmod'];
    $numeroNISS = $_POST['numeroNISS'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateDeNaissance = $_POST['dateDeNaissance'];
    $rue = $_POST['rue'];
    $numeroDomicile = $_POST['numeroDomicile'];
    $ville = $_POST['ville'];
    $sexe = $_POST['sexe'];
    $numeroInami = $_POST['numeroInami'];
    $numeroAssu=$_POST['idAssurabilite'];

    $updateFields = [];

    if (!empty($numeroNISS)) {
        $updateFields[] = "numeroNISS = '$numeroNISS'";
    }
    if (!empty($nom)) {
        $updateFields[] = "nom = '$nom'";
    }
    if (!empty($prenom)) {
        $updateFields[] = "prenom = '$prenom'";
    }
    if (!empty($dateDeNaissance)) {
        $updateFields[] = "dateDeNaissance = '$dateDeNaissance'";
    }
    if (!empty($rue)) {
        $updateFields[] = "rue = '$rue'";
    }
    if (!empty($numeroDomicile)) {
        $updateFields[] = "numeroDomicile = '$numeroDomicile'";
    }
    if (!empty($ville)) {
        $updateFields[] = "ville = '$ville'";
    }
    if (!empty($sexe)) {
        $updateFields[] = "sexe = '$sexe'";
    }
    if (!empty($numeroInami)) {
        $updateFields[] = "numeroInami = '$numeroInami'";
    }
    if (!empty($numeroAssu)) {
        $updateFields[] = "idAssurabilite = '$numeroAssu'";
    }
    
    if (count($updateFields) > 0) {
        $sql = "UPDATE patient SET " . implode(', ', $updateFields) . " WHERE numeroNISS = $idmod";
    
        echo $sql;
        $base->exec($sql);

    } else {
        echo "Aucune donnée à mettre à jour.";
        exit;
    }

    $base->exec($sql);

    echo "<h3>Le rapport avec l'ID $idmod a été modifié avec succès.</h3>";
} catch (Exception $e) {
   
    die('Erreur : ' . $e->getMessage());
}
?>