<?php
include '../config/db.php';

// ⚙️ Fonction de calcul du classement pour une poule donnée
function getClassement($poule, $pdo) {
    // Récupère toutes les équipes de la poule
    $equipes = $pdo->prepare("SELECT * FROM equipes WHERE poule = ?");
    $equipes->execute([$poule]);
    $equipes = $equipes->fetchAll();

    // Initialiser tableau de classement
    $classement = [];

    foreach ($equipes as $equipe) {
        $classement[$equipe['id']] = [
            'nom' => $equipe['nom'],
            'joues' => 0,
            'gagnes' => 0,
            'nuls' => 0,
            'perdus' => 0,
            'bp' => 0, // buts pour
            'bc' => 0, // buts contre
            'diff' => 0,
            'pts' => 0
        ];
    }

    // Récupère tous les matchs de la poule
    $matchs = $pdo->prepare("SELECT * FROM matchs WHERE phase = ?");
    $matchs->execute(['Poule ' . $poule]);
    $matchs = $matchs->fetchAll();

    // Calcul du classement
    foreach ($matchs as $match) {
        $e1 = $match['equipe1_id'];
        $e2 = $match['equipe2_id'];
        $s1 = $match['score1'];
        $s2 = $match['score2'];

        if ($s1 === null || $s2 === null) continue;

        $classement[$e1]['joues']++;
        $classement[$e2]['joues']++;

        $classement[$e1]['bp'] += $s1;
        $classement[$e1]['bc'] += $s2;

        $classement[$e2]['bp'] += $s2;
        $classement[$e2]['bc'] += $s1;

        // Résultat
        if ($s1 > $s2) {
            $classement[$e1]['gagnes']++;
            $classement[$e1]['pts'] += 3;
            $classement[$e2]['perdus']++;
        } elseif ($s1 < $s2) {
            $classement[$e2]['gagnes']++;
            $classement[$e2]['pts'] += 3;
            $classement[$e1]['perdus']++;
        } else {
            $classement[$e1]['nuls']++;
            $classement[$e2]['nuls']++;
            $classement[$e1]['pts'] += 1;
            $classement[$e2]['pts'] += 1;
        }
    }

    // Calcul de la différence de buts
    foreach ($classement as &$team) {
        $team['diff'] = $team['bp'] - $team['bc'];
    }

    // Tri du classement
    usort($classement, function($a, $b) {
        if ($a['pts'] != $b['pts']) return $b['pts'] - $a['pts'];
        if ($a['diff'] != $b['diff']) return $b['diff'] - $a['diff'];
        return $b['bp'] - $a['bp'];
    });

    return $classement;
}

// 🏁 Afficher classement de chaque poule
function afficherClassement($poule, $pdo) {
    $classement = getClassement($poule, $pdo);

    echo "<h2>Classement Poule $poule</h2>";
    echo "<table border='1' cellpadding='10'>
        <thead>
            <tr>
                <th>Équipe</th>
                <th>J</th>
                <th>G</th>
                <th>N</th>
                <th>P</th>
                <th>BP</th>
                <th>BC</th>
                <th>Diff</th>
                <th>Pts</th>
            </tr>
        </thead><tbody>";

    foreach ($classement as $team) {
        echo "<tr>
            <td>{$team['nom']}</td>
            <td>{$team['joues']}</td>
            <td>{$team['gagnes']}</td>
            <td>{$team['nuls']}</td>
            <td>{$team['perdus']}</td>
            <td>{$team['bp']}</td>
            <td>{$team['bc']}</td>
            <td>{$team['diff']}</td>
            <td><strong>{$team['pts']}</strong></td>
        </tr>";
    }

    echo "</tbody></table><br>";
}

// 🔥 Affichage des deux poules
afficherClassement('A', $pdo);
afficherClassement('B', $pdo);
