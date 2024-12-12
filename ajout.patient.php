<?php
session_start();
$numeroInami = $_SESSION['numeroInami']; // l'inamie de l'infirmière 
// Vérification que l'infirmière est connectée
if (!isset($_SESSION['numeroInami'])) {
    echo "Accès refusé. Veuillez vous connecter.";
    exit();
}

$numeroNISS = $_POST['numeroNISS'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$dateDeNaissance = $_POST['dateDeNaissance'];
$rue = $_POST['rue'];
$numeroDomicile = $_POST['numeroDomicile'];
$ville = $_POST['ville'];
$sexe = $_POST['sexe'];
$numeroInamiMedecin = empty($_POST['numeroInamiMedecin']) ? null : $_POST['numeroInamiMedecin'];
$numeroAssu=$_POST['idAssurabilite'];

/*
echo "Numéro NISS : ".$numeroNISS;
echo "<br>";
echo "Nom : ".$nom;echo "<br>";
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
echo "Numéro Inami du médecin traitant (optionnel) : ".$numeroInamiMedecin;
echo "<br>";
*/
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
numeroDomicile, ville, sexe, numeroInamiMedecin, idAssurabilite) VALUES ($numeroNISS,
'$nom', '$prenom', '$dateDeNaissance', '$rue', $numeroDomicile, '$ville', '$sexe',
" . (isset($numeroInamiMedecin) ? $numeroInamiMedecin : "NULL") . ",'$numeroAssu')";

echo $sql;
$Resultat = $base->exec($sql);
echo $Resultat;

if ($Resultat ==true){
	echo "Patient ajouté avec succès !<br>";
        $idVisite=113; // on met 3 mais cette valeur est a changé par 1 car l'id de la visite ne sera jamais associer a un rapport il faut juste la mettre en tant que ligne nécessaire dans le code qui permettra juste de remplir la table encode pour associer le patient au numéro inamie !
        // Association du patient avec l'infirmière dans la table `encode`
        $sqlEncode = "INSERT INTO encode (numeroInami, numeroNISS,idVisite) VALUES (:numeroInami, :numeroNISS,:idVisite)";
        $stmtEncode = $base->prepare($sqlEncode);
        $stmtEncode->bindParam(':numeroInami', $numeroInami);
        $stmtEncode->bindParam(':numeroNISS', $numeroNISS);
        $stmtEncode->bindParam(':idVisite', $idVisite);
        $stmtEncode->execute();
	header("Location:home.patient.html");
	exit();
}
?>