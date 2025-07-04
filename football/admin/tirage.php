<?php
include '../config/db.php';

// Récupérer les équipes
$equipes = $pdo->query("SELECT * FROM equipes ORDER BY RAND()")->fetchAll();

$pouleA = array_slice($equipes, 0, 3);
$pouleB = array_slice($equipes, 3, 2);

// Mettre à jour la BDD
foreach ($pouleA as $eq) {
    $pdo->prepare("UPDATE equipes SET poule = 'A' WHERE id = ?")->execute([$eq['id']]);
}
foreach ($pouleB as $eq) {
    $pdo->prepare("UPDATE equipes SET poule = 'B' WHERE id = ?")->execute([$eq['id']]);
}
?>

<h2>Tirage terminé !</h2>
<h3>Poule A :</h3>
<ul>
    <?php foreach($pouleA as $e): echo "<li>{$e['nom']}</li>"; endforeach; ?>
</ul>

<h3>Poule B :</h3>
<ul>
    <?php foreach($pouleB as $e): echo "<li>{$e['nom']}</li>"; endforeach; ?>
</ul>
