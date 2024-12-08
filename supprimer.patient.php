<?php

$supp=$_POST['numeroNiss'];

if(isset($supp))
{	
	echo "ok";
	$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
	echo "Connexion réussie à la base de données<br>";

    $sql = "delete from patient where numeroNiss='$supp'";
	$base->exec($sql);
    echo "<br>le client ".$supp." est supprimé";
	header("location:afficher.patient.html");
}
?>