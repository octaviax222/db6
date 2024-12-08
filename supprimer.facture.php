<?php

$supp=$_POST['numeroFatcure'];

if(isset($supp))
{	
	$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
	echo "Connexion réussie à la base de données<br>";

    $sql = "delete from facturation where idFacturation='$supp'";
	$base->exec($sql);
    echo "<br>la facture ".$supp." est supprimé";
}
?>