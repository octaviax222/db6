<?php
session_start();

// Vérifier si le nom de l'infirmière est défini, sinon rediriger vers la page de connexion
if (!isset($_SESSION['numeroInami'])) {
    echo "rien";
    exit();
}

// Récupérer le nom de l'infirmière depuis la session
$nomInfirmiere = $_SESSION['numeroInami'];
$prenom = $_SESSION['prenom'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Connexion</title>
    <!-- Lien vers le fichier CSS -->
    <link rel="stylesheet" href="affiche.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Bienvenue sur votre tableau de             bord</h1>
            <div class="user-info">
                Connecté(e) : 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                </svg>
                <strong><?php echo htmlspecialchars($prenom); ?></strong>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="button-row">
            <button type="button" class="btn1"><a href="home.patient.html">PATIENTS</a></button>
            <button type="button" class="btn1"><a href="home.visite.html">VISITES</a></button>
        </div>
        <div class="button-row">
            <button type="button" class="btn1"><a href="home.rapport.html">RAPPORTS</a></button> 
            <button type="button" class="btn1"><a href="home.facturation.html">FACTURATION</a></button>
        </div>
        <div class="button-row">
            <button type="button" class="btn1"><a href="connexion.html">DECONNEXION</a></button>
        </div>
    </div>
</body>
</html>
