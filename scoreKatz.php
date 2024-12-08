<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluation des capacités</title>
</head>
<body>
    <h1>Évaluation des capacités</h1>
    <form action="process_evaluation.php" method="post">
        <!-- Section : Se laver -->
        <h2>Se laver</h2>
        <label><input type="radio" name="se_laver" value="1" required> Capable de se laver complètement sans aucune aide</label><br>
        <label><input type="radio" name="se_laver" value="0.5"> Besoin d'une aide partielle</label><br>
        <label><input type="radio" name="se_laver" value="0"> Totalement dépendant</label><br>

        <!-- Section : S'habiller -->
        <h2>S'habiller</h2>
        <label><input type="radio" name="s_habiller" value="1" required> Capable de s'habiller complètement sans aide</label><br>
        <label><input type="radio" name="s_habiller" value="0.5"> Besoin d'une aide partielle</label><br>
        <label><input type="radio" name="s_habiller" value="0"> Totalement dépendant</label><br>

        <!-- Section : Transfert et déplacement -->
        <h2>Transfert et déplacement</h2>
        <label><input type="radio" name="transfert" value="1" required> Autonome pour les transferts et déplacements</label><br>
        <label><input type="radio" name="transfert" value="0.5"> Besoin d'aide partielle</label><br>
        <label><input type="radio" name="transfert" value="0"> Totalement dépendant</label><br>

        <!-- Section : Aller à la toilette -->
        <h2>Aller à la toilette</h2>
        <label><input type="radio" name="toilette" value="1" required> Capable de gérer la toilette sans aide</label><br>
        <label><input type="radio" name="toilette" value="0.5"> Besoin d'aide partielle</label><br>
        <label><input type="radio" name="toilette" value="0"> Totalement dépendant</label><br>

        <!-- Section : Continence -->
        <h2>Continence</h2>
        <label><input type="radio" name="continence" value="1" required> Totalement continent</label><br>
        <label><input type="radio" name="continence" value="0.5"> Incontinence partielle</label><br>
        <label><input type="radio" name="continence" value="0"> Incontinence complète</label><br>

        <!-- Section : Manger -->
        <h2>Manger</h2>
        <label><input type="radio" name="manger" value="1" required> Capable de manger et boire sans aide</label><br>
        <label><input type="radio" name="manger" value="0.5"> Besoin d'aide partielle</label><br>
        <label><input type="radio" name="manger" value="0"> Totalement dépendant</label><br>

        <!-- Validation -->
        <h2>Personne désorientée dans le temps et l'espace</h2>
        <label><input type="radio" name="desorientation" value="oui" required> Oui</label><br>
        <label><input type="radio" name="desorientation" value="non"> Non</label><br>

        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
