<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
$numeroInami = $_SESSION['numeroInami'];
// Connexion à la base de données
try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    // Préparer la requête SQL
    $sql = ("
        SELECT  
            prestataire.numeroInami AS prestataireNumeroInami,
            prestataire.nom AS prestataireNom,
            prestataire.prenom AS prestatairePrenom,

            patient.numeroNiss AS patientNumeroNiss,
            patient.nom AS patientNom,
            patient.prenom AS patientPrenom,
            patient.idAssurabilite AS patientIdAssurabilite,

            assurabilité.idAssurabilite AS assurabilitéIdAssurabilite,
            assurabilité.organismeAssureur AS assurabilitéOrganismeAssureur,
            assurabilité.typeAssurabilite AS assurabilitéTypeAssurabilite,

            encode.numeroNiss AS encodeNumeroNiss,
            encode.idVisite AS encodeIdVisite,
            encode.numeroInami AS encodeNumeroInami,

            visite.dateR AS visiteDateR,
            visite.idVisite AS visiteIdVisite,

            realise.idSoins AS realiseIdsoins,
            realise.idVisite as realiseIdVisite,

            soins.idSoins AS soinsIdSoins,
            soins.idFacturation AS soinsIdFacturation,

            toilette.forfait AS toiletteForfait,
            toilette.idToilette AS toiletteIdToilette,

            typeSoins.idInamiTypeSoins AS typeSoinsIdInamiTypeSoins,

            facturation.idFacturation AS facturationIdFacturation,
            facturation.dateFacturation AS facturationDateFacturation
        FROM
            encode
        JOIN prestataire ON encode.numeroInami = prestataire.numeroInami
        JOIN patient ON encode.numeroNiss = patient.numeroNiss
        JOIN assurabilité ON patient.idAssurabilite = assurabilité.idAssurabilite
        JOIN visite ON encode.idVisite = visite.idVisite
        JOIN realise ON visite.idVisite = realise.idVisite
        JOIN soins ON realise.idSoins = soins.idSoins
        LEFT JOIN toilette ON soins.idToilette = toilette.idToilette
        JOIN typeSoins ON soins.idInamiTypeSoins = typeSoins.idInamiTypeSoins
        JOIN facturation ON soins.idFacturation = facturation.idFacturation
        WHERE
        encode.numeroInami = :numeroInami AND
        visite.dateR BETWEEN :dateDebut AND :dateFin;
    ");
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$dateDebut = $_POST['dateDebut'] ?? null;
$dateFin = $_POST['dateFin'] ?? null;
if (empty($dateDebut) || empty($dateFin)) {
die("Erreur : les dates début et fin doivent être fournies.");
}
$stmt = $base->prepare($sql);
$stmt->bindParam(":numeroInami", $numeroInami);
$stmt->bindParam(":dateDebut", $dateDebut);
$stmt->bindParam(":dateFin", $dateFin);
$stmt->execute();
$facturations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
$facturations = [];
}
$toiletteForfait = $_POST['toiletteForfait'] ?? 'Pas de forfait';
// Si le bouton 'generate_pdf' est cliqué, générer le PDF
if (isset($_POST['generate_pdf']) && !empty($facturations)) {
require_once('vendor/autoload.php'); // Inclure TCPDF
// Créer un nouvel objet TCPDF
$pdf = new TCPDF();
$pdf->AddPage();
// Définir le titre
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Liste des Facturations', 0, 1, 'C');
// Définir le style du tableau
$pdf->SetFont('helvetica', '', 9);
$pdf->Ln(10);
// En-têtes de colonnes
$pdf->SetFont('helvetica', 'B', 10); // Police pour les en-têtes
$pdf->MultiCell(23, 10, 'Date facturation', 1, 'C', 0, 0);
$pdf->MultiCell(20, 10, 'Date visite', 1, 'C', 0, 0);
$pdf->MultiCell(23, 10, 'Nom Prestataire', 1, 'C', 0, 0);
$pdf->MultiCell(23, 10, 'Prénom Prestataire', 1, 'C', 0, 0);
$pdf->MultiCell(25, 10, 'Numéro NISS Patient', 1, 'C', 0, 0);
$pdf->MultiCell(20, 10, 'Assurabilité', 1, 'C', 0, 0);
$pdf->MultiCell(20, 10, 'Assurabilité', 1, 'C', 0, 0);
$pdf->MultiCell(26, 10, 'Numéro INAMI Soin', 1, 'C', 0, 0);
$pdf->MultiCell(15, 10, 'Forfait', 1, 'C', 0, 1);
// Remplir les données
foreach ($facturations as $ligne) {
        $toiletteForfait = $ligne['toiletteForfait'] ?? 'Pas de forfait'; 
        $pdf->MultiCell(23, 10, $ligne['facturationDateFacturation'], 1, 'C', 0, 0);
        $pdf->MultiCell(20, 10, $ligne['visiteDateR'], 1, 'C', 0, 0);
        $pdf->MultiCell(23, 10, $ligne['prestataireNom'], 1, 'C', 0, 0);
        $pdf->MultiCell(23, 10, $ligne['prestatairePrenom'], 1, 'C', 0, 0);
        $pdf->MultiCell(25, 10, $ligne['patientNumeroNiss'], 1, 'C', 0, 0);
        $pdf->MultiCell(20, 10, $ligne['assurabilitéOrganismeAssureur'], 1, 'C', 0, 0);
        $pdf->MultiCell(20, 10, $ligne['assurabilitéTypeAssurabilite'], 1, 'C', 0, 0);
        $pdf->MultiCell(26, 10, $ligne['typeSoinsIdInamiTypeSoins'], 1, 'C', 0, 0);
        $pdf->MultiCell(15, 10, $toiletteForfait, 1, 'C', 0, 1); // Notez le dernier paramètre "1" pour un saut de ligne
    
}
// Enregistrer le PDF ou l'afficher
$pdf->Output('facturations.pdf', 'D'); // 'D' pour télécharger le fichier
}
} catch (Exception $e) {
echo "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Liste des facturations</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
<div class="w3-container">
<div class="col">
<button class="btn btn-primary" style="margin-top: 32px;">
<a href="home.facturation.html" style="color: white; text-decoration: none;">Retour</a>
</button>
</div>
<h1 class="text-center mb-4">Liste des facturations</h1>
<!-- Formulaire de sélection des dates -->
<form method="POST" class="mb-4">
<div class="form-row">
<div class="col">
<label for="dateDebut">Date de début</label>
<input type="date" id="dateDebut" name="dateDebut" class="form-control" required>
</div>
<div class="col">
<label for="dateFin">Date de fin</label>
<input type="date" id="dateFin" name="dateFin" class="form-control" required>
</div>
<div class="col">
<button type="submit" class="btn btn-primary" style="margin-top: 32px;">Filtrer</button>
</div>
<div class="col">
<!-- Bouton "Générer PDF" avec le même style -->
<button type="submit" name="generate_pdf" class="btn btn-primary" style="margin-top: 32px;">Générer PDF</button>
</div>
</div>
</form>
<!-- Tableau des facturations -->
<table class="table table-bordered table-striped">
<thead class="thead-dark">
<tr>
<th>Numéro de la facturation</th>
<th>Date de la facturation</th>
<th>Nom du prestataire</th>
<th>Prenom du prestataire</th>
<th>Numero NISS du patient</th>
<th>Nom du patient</th>
<th>Prenom du patient</th>
<th>Date de la visite du patient</th>
<th>Numero INAMI du soin</th>
<th>Forfait du patient</th>
</tr>
</thead>
<tbody>
<?php if (!empty($facturations)) : ?>
<?php foreach ($facturations as $ligne) : ?>
    <?php 
        $forfait = $ligne['toiletteForfait'] ?? 'Pas de forfait'; // Gérer NULL
    ?>
<tr>
<td><?= htmlspecialchars($ligne['facturationIdFacturation']) ?></td>
<td><?= htmlspecialchars($ligne['facturationDateFacturation']) ?></td>
<td><?= htmlspecialchars($ligne['prestataireNom']) ?></td>
<td><?= htmlspecialchars($ligne['prestatairePrenom']) ?></td>
<td><?= htmlspecialchars($ligne['patientNumeroNiss']) ?></td>
<td><?= htmlspecialchars($ligne['patientNom']) ?></td>
<td><?= htmlspecialchars($ligne['patientPrenom']) ?></td>
<td><?= htmlspecialchars($ligne['visiteDateR']) ?></td>
<td><?= htmlspecialchars($ligne['typeSoinsIdInamiTypeSoins']) ?></td>
<td><?= htmlspecialchars($forfait) ?></td> <!-- Utiliser la valeur gérée -->
<td><a href="supprimer.facture.php?chkid=<?= $ligne['facturationIdFacturation'] ?>" class="btn btn-danger btn-sm">Supprimer</a></td>
</tr>
<?php endforeach; ?>
<?php else : ?>
<tr>
<td colspan="11" class="text-center">Aucune facturation trouvée pour ces dates.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>