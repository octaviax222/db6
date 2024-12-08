<?php

$supp=$_POST['idVisite'];

if(isset($supp))
{	
	echo "ok";
	$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
	echo "Connexion réussie à la base de données<br>";

    $sql = "delete from visite where idVisite='$supp'";
	$base->exec($sql);
    echo "<br>la visite ".$supp." est supprimée";
	header("location:afficher.visite.html");
}
?>