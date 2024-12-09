<?php
header('Content-Type: text/html; charset=utf-8');

try {
    $base = new PDO('mysql:host=143.47.179.70:443;dbname=db6', 'user6', 'user6');
    $sql = "SELECT 
            p.numeroNiss, 
            p.nom, 
            p.prenom, 
            p.dateDeNaissance, 
            p.rue, 
            p.numeroDomicile, 
            p.ville, 
            p.sexe, 
            p.numeroInami, 
            a.organismeAssureur, 
            a.typeAssurabilite
        FROM 
            patient p
        LEFT JOIN 
            assurabilité a 
        ON 
            p.idAssurabilite = a.idAssurabilite";

    $resultat = $base->query($sql);
    
    while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
        $niss = htmlspecialchars($ligne['numeroNiss']);
        $nom = htmlspecialchars($ligne['nom']);
        $prenom = htmlspecialchars($ligne['prenom']);
        $dateNaissance = htmlspecialchars($ligne['dateDeNaissance']);
        $rue = htmlspecialchars($ligne['rue']);
        $num = htmlspecialchars($ligne['numeroDomicile']);
        $ville = htmlspecialchars($ligne['ville']);
        $sexe = htmlspecialchars($ligne['sexe']);
        $inami = isset($ligne['numeroInami']) ? htmlspecialchars($ligne['numeroInami']) : "";
        $assureur = htmlspecialchars($ligne['organismeAssureur']);
        $typeAssurabilite = htmlspecialchars($ligne['typeAssurabilite']);

        echo "<tr>";
        echo "<td>$niss</td>";
        echo "<td>$nom</td>";
        echo "<td>$prenom</td>";
        echo "<td>$dateNaissance</td>";
        echo "<td>$rue</td>";
        echo "<td>$num</td>";
        echo "<td>$ville</td>";
        echo "<td>$sexe</td>";
        echo "<td>$inami</td>";
        echo "<td>$assureur - $typeAssurabilite</td>";
        echo "<td><a href='supprimer.html?chkid=$niss' class='btn btn-danger btn-sm'>Supprimer</a></td>";
        echo "</tr>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>