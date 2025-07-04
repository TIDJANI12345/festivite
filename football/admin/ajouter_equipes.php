<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $stmt = $pdo->prepare("INSERT INTO equipes (nom) VALUES (?)");
    $stmt->execute([$nom]);
}

$equipes = $pdo->query("SELECT * FROM equipes")->fetchAll();
?>

<h2>Ajouter une équipe</h2>
<form method="POST">
    <input type="text" name="nom" required placeholder="Nom de l’équipe" />
    <button type="submit">Ajouter</button>
</form>

<h3>Équipes enregistrées :</h3>
<ul>
    <?php foreach($equipes as $e): ?>
        <li><?= $e['nom'] ?></li>
    <?php endforeach; ?>
</ul>
