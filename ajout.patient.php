<?php
session_start();
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
<<<<<<< HEAD
$numeroInamiMedecin = empty($_POST['numeroInamiMedecin']) ? null : $_POST['numeroInamiMedecin'];
=======
$numeroInami = $_POST['numeroInami'];
>>>>>>> 2123a1e4d803b08ae6ef875ed161adc271d67b96
$numeroAssu=$_POST['idAssurabilite'];
$numeroInami_inf=$_SESSION['numeroInami']; // l'inamie de l'infirmière 
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
$sql = "INSERT INTO patient(numeroNiss, nom, prenom, dateDeNaissance, rue,
numeroDomicile, ville, sexe, numeroInami, idAssurabilite) VALUES ($numeroNISS,
<<<<<<< HEAD
'$nom', '$prenom', '$dateDeNaissance', '$rue', $numeroDomicile, '$ville', '$sexe'," . 
        (isset($numeroInamiMedecin) ? $numeroInamiMedecin : "NULL") . ",'$numeroAssu')";

=======
'$nom', '$prenom', '$dateDeNaissance', '$rue', $numeroDomicile, '$ville', '$sexe',
1213,'$numeroAssu')";
>>>>>>> 2123a1e4d803b08ae6ef875ed161adc271d67b96
echo $sql;
$Resultat = $base->exec($sql);
echo $Resultat;
if ($Resultat ==true){
	echo "Patient ajouté avec succès !<br>";
        $idVisite=52; // on met 3 mais cette valeur est a changé par 1 car l'id de la visite ne sera jamais associer a un rapport il faut juste la mettre en tant que ligne nécessaire dans le code qui permettra juste de remplir la table encode pour associer le patient au numéro inamie !
        // Association du patient avec l'infirmière dans la table `encode`
        $sqlEncode = "INSERT INTO encode (numeroInami, numeroNISS,idVisite) VALUES (:numeroInami, :numeroNISS,:idVisite)";
        $stmtEncode = $base->prepare($sqlEncode);
        $stmtEncode->bindParam(':numeroInami', $numeroInami_inf);
        $stmtEncode->bindParam(':numeroNISS', $numeroNISS);
        $stmtEncode->bindParam(':idVisite', $idVisite);
        $stmtEncode->execute();
	header("Location:home.patient.html");
	exit();
}

?>