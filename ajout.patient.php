<?php
session_start();

// Vérification que l'infirmière est connectée
if (!isset($_SESSION['numeroInami'])) {
    echo "Accès refusé. Veuillez vous connecter.";
    exit();
}

$numeroInami = $_SESSION['numeroInami']; // Numéro Inami de l'infirmière

// Connexion à la base de données
try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeroNISS = $_POST['numeroNISS'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateDeNaissance = $_POST['dateDeNaissance'];
    $rue = $_POST['rue'];
    $numeroDomicile = $_POST['numeroDomicile'];
    $ville = $_POST['ville'];
    $sexe = $_POST['sexe'];
    $numeroInamiMedecin = empty($_POST['numeroInamiMedecin']) ? null : $_POST['numeroInamiMedecin'];
    $numeroAssu = $_POST['idAssurabilite'];

    // Vérification de l'existence du numéro NISS
    $sqlCheck = "SELECT COUNT(*) FROM patient WHERE numeroNiss = :numeroNiss";
    $stmtCheck = $base->prepare($sqlCheck);
    $stmtCheck->bindParam(':numeroNiss', $numeroNISS, PDO::PARAM_INT);
    $stmtCheck->execute();
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        echo "<script>alert('Erreur : Le patient avec le numéro NISS $numeroNISS existe déjà.');</script>";
    } else {
        // Insertion du patient
        $sql = "INSERT INTO patient(numeroNiss, nom, prenom, dateDeNaissance, rue, numeroDomicile, ville, sexe, numeroInamiMedecin, idAssurabilite) 
                VALUES (:numeroNISS, :nom, :prenom, :dateDeNaissance, :rue, :numeroDomicile, :ville, :sexe, :numeroInamiMedecin, :idAssurabilite)";
        $stmt = $base->prepare($sql);
        $stmt->bindParam(':numeroNISS', $numeroNISS);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':dateDeNaissance', $dateDeNaissance);
        $stmt->bindParam(':rue', $rue);
        $stmt->bindParam(':numeroDomicile', $numeroDomicile);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':sexe', $sexe);
        $stmt->bindParam(':numeroInamiMedecin', $numeroInamiMedecin);
        $stmt->bindParam(':idAssurabilite', $numeroAssu);

        if ($stmt->execute()) {
            // Association dans la table `encode`
            $idVisite = 121; // À ajuster dynamiquement si nécessaire
            $sqlEncode = "INSERT INTO encode (numeroInami, numeroNiss, idVisite) VALUES (:numeroInami, :numeroNISS, :idVisite)";
            $stmtEncode = $base->prepare($sqlEncode);
            $stmtEncode->bindParam(':numeroInami', $numeroInami);
            $stmtEncode->bindParam(':numeroNISS', $numeroNISS);
            $stmtEncode->bindParam(':idVisite', $idVisite);
            $stmtEncode->execute();

            header("Location: home.patient.html");
            exit();
        } else {
            echo "Erreur lors de l'ajout du patient.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un patient</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="col">
            <a href="home.patient.html" class="btn btn-primary" style="margin-top: 32px;">Retour</a>
        </div>
        <h1 class="text-center mb-4">Ajouter un Patient</h1>
        <form method="POST" action="ajout.patient.php">
            <div class="form-group">
                <label for="numeroNISS">Numéro NISS</label>
                <input type="text" class="form-control" id="numeroNISS" name="numeroNISS" required>
            </div>
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="dateDeNaissance">Date de Naissance</label>
                <input type="date" class="form-control" id="dateDeNaissance" name="dateDeNaissance" required>
            </div>
            <div class="form-group">
                <label for="rue">Rue</label>
                <input type="text" class="form-control" id="rue" name="rue" required>
            </div>
            <div class="form-group">
                <label for="numeroDomicile">Numéro de Domicile</label>
                <input type="text" class="form-control" id="numeroDomicile" name="numeroDomicile" required>
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" class="form-control" id="ville" name="ville" required>
            </div>
            <div class="form-group">
                <label for="sexe">Sexe</label>
                <select class="form-control" id="sexe" name="sexe" required>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numeroInamiMedecin">Numéro Inami du médecin traitant (optionnel)</label>
                <select class="form-control" id="numeroInamiMedecin" name="numeroInamiMedecin">
                    <option value="">-- Sélectionnez un médecin traitant --</option>
                    <?php
                    $sql = "SELECT numeroInamiMedecin, nom, prenom FROM medecintraitant ORDER BY nom, prenom";
                    $stmt = $base->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($row['numeroInamiMedecin']) . '">' . htmlspecialchars($row['nom']) . ' - ' . htmlspecialchars($row['prenom']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="idAssurabilite">Numéro Assurabilité</label>
                <select class="form-control" id="idAssurabilite" name="idAssurabilite" required>
                    <option value="">-- Sélectionnez une assurabilité --</option>
                    <?php
                    $sql = "SELECT idAssurabilite, organismeAssureur, typeAssurabilite FROM assurabilité";
                    $stmt = $base->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($row['idAssurabilite']) . '">' . htmlspecialchars($row['organismeAssureur']) . ' - ' . htmlspecialchars($row['typeAssurabilite']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Ajouter le Patient</button>
        </form>
    </div>
</body>
</html>
