<?php
include '../config/db.php';

function genererMatchs($poule) {
    global $pdo;
    $equipes = $pdo->prepare("SELECT * FROM equipes WHERE poule = ?");
    $equipes->execute([$poule]);
    $equipes = $equipes->fetchAll();

    for ($i = 0; $i < count($equipes); $i++) {
        for ($j = $i + 1; $j < count($equipes); $j++) {
            $stmt = $pdo->prepare("INSERT INTO matchs (equipe1_id, equipe2_id, phase) VALUES (?, ?, ?)");
            $stmt->execute([$equipes[$i]['id'], $equipes[$j]['id'], 'Poule ' . $poule]);
        }
    }
}

genererMatchs('A');
genererMatchs('B');

echo "Matchs générés avec succès !";
?>
