<?php
session_start();
include('includes/config.php');

// 🔐 Authentification simple
if (!isset($_SESSION['admin_logged'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
        if ($_POST['username'] === 'admin' && $_POST['password'] === '123456') {
            $_SESSION['admin_logged'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = "❌ Identifiants invalides.";
        }
    }

    // Formulaire de connexion
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Connexion Admin</title>
        <style>
            body { background-color: #f3f4f6; font-family: sans-serif; }
            .login-box {
                max-width: 400px; margin: 80px auto; background: #fff;
                padding: 20px; border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            input, button {
                width: 100%; padding: 10px;
                margin-bottom: 15px; border: 1px solid #ccc;
                border-radius: 4px;
            }
            button {
                background: #f97316; color: white; border: none;
                cursor: pointer;
            }
            button:hover { background: #ea580c; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>🔐 Connexion Administrateur</h2>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= $error ?></p>
            <?php endif; ?>
            <form method="post">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" required>
                <label>Mot de passe</label>
                <input type="password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
        </div>
    </body>
    </html>
    <?php exit;
}

// 🔓 Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

include('includes/header.php');

// Mise à jour des scores et/ou date
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $match_id = (int)$_POST['match_id'];
    $score1 = is_numeric($_POST['score1']) ? (int)$_POST['score1'] : null;
    $score2 = is_numeric($_POST['score2']) ? (int)$_POST['score2'] : null;
    $date_match = !empty($_POST['date_match']) ? $_POST['date_match'] : null;

    $stmt = $pdo->prepare("UPDATE matchs SET score1 = ?, score2 = ?, date_match = ?, statut = 'terminé' WHERE id = ?");
    $stmt->execute([$score1, $score2, $date_match, $match_id]);

    echo "<div class='bg-green-100 text-green-700 p-3 my-4 rounded max-w-4xl mx-auto'>✅ Match mis à jour.</div>";
}

// Affichage poule par poule
$poules = $pdo->query("SELECT * FROM poules")->fetchAll(PDO::FETCH_ASSOC);
echo "<a href='?logout=1' class='text-sm text-red-600 float-right mr-4'>🚪 Se déconnecter</a>";

foreach ($poules as $poule) {
    $poule_id = $poule['id'];
    $poule_nom = htmlspecialchars($poule['nom']);

    echo "<section class='max-w-5xl mx-auto mt-10 p-4 bg-white rounded shadow'>";
    echo "<h2 class='text-2xl font-bold text-orange-600 mb-4'>⚽️ Poule $poule_nom</h2>";

    // Équipes
    $stmt = $pdo->prepare("SELECT e.nom FROM equipe_poule ep JOIN equipes e ON ep.equipe_id = e.id WHERE ep.poule_id = ?");
    $stmt->execute([$poule_id]);
    $equipes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<div class='grid grid-cols-2 md:grid-cols-4 gap-4 mb-6'>";
    foreach ($equipes as $eq) {
        echo "<div class='bg-blue-100 text-blue-900 p-3 rounded text-center font-semibold shadow'>$eq</div>";
    }
    echo "</div>";
    // Matchs
    $stmt = $pdo->prepare("
        SELECT m.*, e1.nom AS equipe1, e2.nom AS equipe2 
        FROM matchs m 
        JOIN equipes e1 ON m.equipe1_id = e1.id 
        JOIN equipes e2 ON m.equipe2_id = e2.id 
        WHERE m.poule_id = ? ORDER BY m.date_match
    ");
    $stmt->execute([$poule_id]);
    $matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3 class='text-lg font-semibold mb-2'>Matchs & Programmation</h3>";

    foreach ($matchs as $match) {
        $e1 = htmlspecialchars($match['equipe1']);
        $e2 = htmlspecialchars($match['equipe2']);
        $s1 = $match['score1'];
        $s2 = $match['score2'];
        $id = $match['id'];
        $date = $match['date_match'] ? date('Y-m-d\TH:i', strtotime($match['date_match'])) : '';

        echo "<form method='post' class='bg-white border rounded shadow p-3 mb-3 grid grid-cols-1 md:grid-cols-6 gap-2 items-center'>";
        echo "<input type='hidden' name='match_id' value='$id'>";
        echo "<input type='datetime-local' name='date_match' value='$date' class='border rounded px-2 py-1 col-span-2'>";
        echo "<div class='flex items-center justify-center gap-2 col-span-1'>
                <span class='font-semibold'>$e1</span>
                <input type='number' name='score1' value='" . ($s1 ?? '') . "' class='w-14 text-center border rounded'>
              </div>";
        echo "<div class='flex items-center justify-center gap-2 col-span-1'>
                <input type='number' name='score2' value='" . ($s2 ?? '') . "' class='w-14 text-center border rounded'>
                <span class='font-semibold'>$e2</span>
              </div>";
        echo "<button type='submit' class='bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded col-span-1'>💾</button>";
        echo "</form>";
    }

    // Classement
    $classement = [];
    foreach ($equipes as $eq) {
        $classement[$eq] = ['J'=>0,'G'=>0,'N'=>0,'P'=>0,'BP'=>0,'BC'=>0,'DIFF'=>0,'PTS'=>0];
    }

    foreach ($matchs as $m) {
        if ($m['score1'] === null ||  $m['score2'] === null) continue;
        $e1 = $m['equipe1'];
        $e2 = $m['equipe2'];
        $s1 = $m['score1'];
        $s2 = $m['score2'];

        $classement[$e1]['J']++;
        $classement[$e2]['J']++;
        $classement[$e1]['BP'] += $s1;
        $classement[$e1]['BC'] += $s2;
        $classement[$e2]['BP'] += $s2;
        $classement[$e2]['BC'] += $s1;

        if ($s1 > $s2) {
            $classement[$e1]['G']++;
            $classement[$e1]['PTS'] += 3;
            $classement[$e2]['P']++;
        } elseif ($s1 < $s2) {
            $classement[$e2]['G']++;
            $classement[$e2]['PTS'] += 3;
            $classement[$e1]['P']++;
        } else {
            $classement[$e1]['N']++;
            $classement[$e2]['N']++;
            $classement[$e1]['PTS']++;
            $classement[$e2]['PTS']++;
        }
    }

    foreach ($classement as &$stat) {
        $stat['DIFF'] = $stat['BP'] - $stat['BC'];
    }

    uasort($classement, fn($a, $b) => [$b['PTS'], $b['DIFF'], $b['BP']] <=> [$a['PTS'], $a['DIFF'], $a['BP']]);

    echo "<h3 class='mt-6 mb-2 font-semibold'>Classement</h3>";
    echo "<table class='w-full table-auto border border-collapse text-sm'>";
    echo "<thead class='bg-blue-900 text-white'>
        <tr><th class='border px-2'>#</th><th class='border px-2 text-left'>Équipe</th><th class='border px-2'>J</th>
            <th class='border px-2'>G</th><th class='border px-2'>N</th><th class='border px-2'>P</th>
            <th class='border px-2'>BP</th><th class='border px-2'>BC</th><th class='border px-2'>+/-</th><th class='border px-2'>Pts</th></tr></thead><tbody>";

    $pos = 1;
    foreach ($classement as $equipe => $stat) {
        echo "<tr class='" . ($pos % 2 == 0 ? 'bg-gray-100' : '') . "'>
                <td class='border px-2 text-center'>$pos</td>
                <td class='border px-2'>$equipe</td>
                <td class='border px-2 text-center'>{$stat['J']}</td>
                <td class='border px-2 text-center'>{$stat['G']}</td>
                <td class='border px-2 text-center'>{$stat['N']}</td>
                <td class='border px-2 text-center'>{$stat['P']}</td>
                <td class='border px-2 text-center'>{$stat['BP']}</td>
                <td class='border px-2 text-center'>{$stat['BC']}</td>
                <td class='border px-2 text-center'>{$stat['DIFF']}</td>
                <td class='border px-2 text-center font-bold'>{$stat['PTS']}</td>
              </tr>";
        $pos++;
    }
    echo "</tbody></table></section>";
}

// Calendrier général
$matchs = $pdo->query("
    SELECT m.date_match AS date_heure, p.nom AS poule, e1.nom AS equipe1, e2.nom AS equipe2, m.score1, m.score2
    FROM matchs m
    JOIN poules p ON m.poule_id = p.id
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    ORDER BY m.date_match
")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="max-w-6xl mx-auto mt-12 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">📅 Calendrier Général</h2>
    <table class="w-full border border-collapse text-sm">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="border px-2 py-1">Date & Heure</th>
                <th class="border px-2 py-1">Poule</th>
                <th class="border px-2 py-1">Équipe 1</th>
                <th class="border px-2 py-1">Équipe 2</th>
                <th class="border px-2 py-1">Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchs as $m): ?>
                <tr>
                    <td class="border px-2 py-1"><?= $m['date_heure'] ? date('d/m/Y H:i', strtotime($m['date_heure'])) : '⏳ À venir' ?></td>
                    <td class="border px-2 py-1"><?= htmlspecialchars($m['poule']) ?></td>
                    <td class="border px-2 py-1"><?= htmlspecialchars($m['equipe1']) ?></td>
                    <td class="border px-2 py-1"><?= htmlspecialchars($m['equipe2']) ?></td>
                    <td class="border px-2 py-1 text-center">
                        <?= is_null($m['score1']) || is_null($m['score2']) ? '⏳' : "{$m['score1']} - {$m['score2']}" ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php include('includes/footer.php'); ?>