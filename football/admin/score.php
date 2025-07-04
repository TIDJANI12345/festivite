<?php
include '../config/db.php';

function genererMatchs($poule) {
    global $pdo;
    $equipes = $pdo->prepare("SELECT * FROM equipes WHERE poule = ?");
    $equipes->execute([$poule]);
    $equipes = $equipes->fetchAll();

    // Supprimer les anciens matchs de cette poule avant de recréer
    $pdo->prepare("DELETE FROM matchs WHERE phase = ?")->execute(['Poule ' . $poule]);

    if ($poule === 'B') {
        // Comme il n'y a que 2 équipes, on crée deux matchs aller-retour
        $equipe1 = $equipes[0];
        $equipe2 = $equipes[1];

        // Match aller
        $stmt = $pdo->prepare("INSERT INTO matchs (equipe1_id, equipe2_id, phase) VALUES (?, ?, ?)");
        $stmt->execute([$equipe1['id'], $equipe2['id'], 'Poule B']);

        // Match retour
        $stmt = $pdo->prepare("INSERT INTO matchs (equipe1_id, equipe2_id, phase) VALUES (?, ?, ?)");
        $stmt->execute([$equipe2['id'], $equipe1['id'], 'Poule B']);
    } else {
        // Pour la poule A (3 équipes), matchs simples aller
        for ($i = 0; $i < count($equipes); $i++) {
            for ($j = $i + 1; $j < count($equipes); $j++) {
                $stmt = $pdo->prepare("INSERT INTO matchs (equipe1_id, equipe2_id, phase) VALUES (?, ?, ?)");
                $stmt->execute([$equipes[$i]['id'], $equipes[$j]['id'], 'Poule ' . $poule]);
            }
        }
    }
}

// Exécute la génération pour les deux poules
genererMatchs('A');
genererMatchs('B');

echo "Matchs générés avec succès avec prise en compte du match aller-retour pour Poule B !";
?>
