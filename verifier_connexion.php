<?php
// Paramètres de connexion à la base de données
$host = '143.47.179.70:443';
$dbname = 'db6';
$userniss = 'user6';
$password = 'user6';

// Connexion à la base de données avec PDO
try {
    $base = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $userniss, $password);
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);;
	echo "Connexion à la base de données réussites !";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupérer les données du formulaire
$numeroInami = $_POST['inami']; // on va chercher le name= de la page html
$password = $_POST['password'];

// Requête SQL pour vérifier les informations de connexion dans la table prestataire
$sql = "SELECT * FROM prestataire WHERE numeroInami = :numeroInami";
$stmt = $base->prepare($sql);
$stmt->bindParam(':numeroInami', $numeroInami);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user) {
	
    // Vérifier si le mot de passe est correct
	if ($password == $user['motDePasse']) {
		echo $user['motDePasse'];
        // Démarrer une session pour l'utilisateur
        session_start();
        $_SESSION['numeroInami'] = $user['numeroInami'];
        $_SESSION['prenom'] = $user['prenom']; // Ajoutez ceci pour stocker le prénom
        $_SESSION['loggedin'] = true;
		
		
        // Rediriger vers la page d'accueil ou un tableau de bord
		header("Location: home.php");
		exit();
        
    } else {
        echo "Mot de passe incorrect.";
		sleep(2);
		header("Location:connexion.html");
		exit();
    }
} else {
    echo "Numéro INAMI non trouvé.";
}
?>
