<?php

$supp=$_POST['numeroRapport'];

if(isset($supp))
{	
	$base = new PDO('mysql:host=143.47.179.70:443;dbname=db6','user6','user6');
	echo "Connexion réussie à la base de données<br>";

    $sql = "delete from rapportpatient where idRapport='$supp'";
	$base->exec($sql);
    echo "<br>le rapport ".$supp." est supprimé";
	header("Location:home.rapport.html");
}
?>