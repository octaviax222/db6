<?php

session_start();
$numeroInamiInfirmiere = $_SESSION['numeroInami'];

$numeroNISS = $_POST['numeroNISS'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$dateDeNaissance = $_POST['dateDeNaissance'];
$rue = $_POST['rue'];
$numeroDomicile = $_POST['numeroDomicile'];
$ville = $_POST['ville'];
$sexe = $_POST['sexe'];
$numeroInami = empty($_POST['numeroInami']) ? null : $_POST['numeroInami'];
$numeroAssu=$_POST['idAssurabilite'];

echo "Numéro NISS : ".$numeroNISS;
echo "<br>";
echo "Nom : ".$nom;
echo "<br>";
echo "Prénom : ".$prenom;
echo "<br>";
echo "Date de Naissance : ".$dateDeNaissance;
echo "<br>";
echo "Rue : : ".$rue;
echo "<br>";
echo "Numéro de rue : ".$numeroDomicile;
echo "<br>";
echo "Ville : ".$ville;
echo "<br>";
echo "Sexe : ".$sexe;
echo "<br>";
echo "Numéro Inami du médecin traitant (optionnel) : ".$numeroInami;
echo "<br>";

$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6', 'user6');
echo"Connexion réussie à la base de données<br>";

// Vérification si le numéro NISS existe déjà dans la base de données
$sqlCheck = "SELECT COUNT(*) FROM patient WHERE numeroNiss = :numeroNiss";
$stmtCheck = $base->prepare($sqlCheck);
$stmtCheck->bindParam(':numeroNiss', $numeroNISS, PDO::PARAM_INT);
$stmtCheck->execute();
$count = $stmtCheck->fetchColumn();

// Si le numéro NISS existe déjà, afficher un message d'erreur et arrêter le script
if ($count > 0) {
    echo "<script>alert('Erreur : Le patient avec le numéro NISS $numeroNISS existe déjà.');</script>";
    header("Location:home.patient.html");
	exit();
}

$sql = "INSERT INTO patient(numeroNiss, nom, prenom, dateDeNaissance, rue,
numeroDomicile, ville, sexe, numeroInami, idAssurabilite) VALUES ($numeroNISS,
'$nom', '$prenom', '$dateDeNaissance', '$rue', $numeroDomicile, '$ville', '$sexe'," . 
        (isset($numeroInami) ? $numeroInami : "NULL") . ",'$numeroAssu')";

echo $sql;
$Resultat = $base->exec($sql);

echo $Resultat;
if ($Resultat ==true){
	header("Location:home.patient.html");
	exit();
}

?>