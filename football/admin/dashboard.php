<?php
include '../config/db.php';

// Récupérer des stats simples
$nbEquipes = $pdo->query("SELECT COUNT(*) FROM equipes")->fetchColumn();

$nbMatchs = $pdo->query("SELECT COUNT(*) FROM matchs")->fetchColumn();

$nbMatchsJoues = $pdo->query("SELECT COUNT(*) FROM matchs WHERE score1 IS NOT NULL AND score2 IS NOT NULL")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin - Tournoi Football</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f4f8; }
        h1 { color: #1e40af; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(30,64,175,0.2); margin-bottom: 20px; }
        nav a { margin-right: 15px; color: #0bbbd6; text-decoration: none; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h1>📊 Tableau de bord Admin - Tournoi Football</h1>

<nav>
    <a href="ajouter_equipes.php">Ajouter / Voir Équipes</a>
    <a href="tirage.php">Tirage des Poules</a>
    <a href="matchs.php">Générer Matchs</a>
    <a href="score.php">Saisir Scores</a>
    <a href="classement.php">Voir Classement</a>
</nav>

<div class="card">
    <h2>Statistiques générales</h2>
    <ul>
        <li>Nombre d’équipes inscrites : <strong><?= $nbEquipes ?></strong></li>
        <li>Nombre total de matchs générés : <strong><?= $nbMatchs ?></strong></li>
        <li>Nombre de matchs joués (avec score) : <strong><?= $nbMatchsJoues ?></strong></li>
    </ul>
</div>

</body>
</html>
