<?php
session_start();
include('includes/config.php');
include('includes/header.php');

// Enregistrement scores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $score1 = is_numeric($_POST['score1']) ? (int)$_POST['score1'] : null;
    $score2 = is_numeric($_POST['score2']) ? (int)$_POST['score2'] : null;
    $match_id = (int)$_POST['match_id'];

    $stmt = $pdo->prepare("UPDATE matchs SET score1 = ?, score2 = ?, statut = 'terminé' WHERE id = ?");
    $stmt->execute([$score1, $score2, $match_id]);

    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mx-auto max-w-3xl mt-4 mb-4'>✅ Score mis à jour avec succès.</div>";
}

// Récupère toutes les poules
$poules = $pdo->query("SELECT * FROM poules")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($poules as $poule): ?>
    <?php
    $poule_id = $poule['id'];
    $poule_nom = htmlspecialchars($poule['nom']);

    // Récupération équipes de la poule (avec ID)
    $sql = "SELECT e.id, e.nom 
            FROM equipe_poule ep 
            JOIN equipes e ON e.id = ep.equipe_id 
            WHERE ep.poule_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$poule_id]);
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <section class='max-w-5xl mx-auto mt-10 p-4 bg-white rounded shadow'>
        <h2 class='text-2xl font-bold text-orange-600 mb-4'>⚽ <?= $poule_nom ?></h2>

        <!-- Boutons équipes cliquables -->
        <div class='grid grid-cols-2 md:grid-cols-4 gap-4 mb-6'>
            <?php foreach ($equipes as $eq): ?>
                <button
                    class='bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow'
                    onclick="openModal(<?= $eq['id'] ?>, '<?= htmlspecialchars(addslashes($eq['nom'])) ?>')"
                >
                    <?= htmlspecialchars($eq['nom']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php
        // Matchs
        $sql = "SELECT m.*, e1.nom AS equipe1, e2.nom AS equipe2 
                FROM matchs m 
                JOIN equipes e1 ON e1.id = m.equipe1_id 
                JOIN equipes e2 ON e2.id = m.equipe2_id 
                WHERE m.poule_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$poule_id]);
        $matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <h3 class='text-lg font-semibold mb-2'>Matchs & Scores</h3>

        <?php foreach ($matchs as $match): 
            $e1 = htmlspecialchars($match['equipe1']);
            $e2 = htmlspecialchars($match['equipe2']);
            $s1 = $match['score1'];
            $s2 = $match['score2'];
            $id = $match['id'];
        ?>
            <?php if ($match['statut'] === 'terminé'): ?>
                <div class='bg-gray-100 rounded p-3 mb-3 flex justify-between items-center'>
                    <span class='font-semibold'><?= "$e1 $s1 - $s2 $e2" ?></span>
                    <span class='text-sm text-gray-500'>(Terminé)</span>
                </div>
            <?php else: ?>
                <form method='post' class='bg-white rounded shadow p-3 mb-3 flex flex-wrap items-center gap-3'>
                    <div class='flex-1 text-center font-semibold'><?= $e1 ?></div>
                    <input type='number' name='score1' value='<?= $s1 ?? '' ?>' class='w-16 text-center border rounded' required>
                    <span class='font-bold'> - </span>
                    <input type='number' name='score2' value='<?= $s2 ?? '' ?>' class='w-16 text-center border rounded' required>
                    <div class='flex-1 text-center font-semibold'><?= $e2 ?></div>
                    <input type='hidden' name='match_id' value='<?= $id ?>'>
                    <button class='bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded' type='submit'>Enregistrer</button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php
        // Calcul classement
        $classement = [];
        foreach ($equipes as $eq) {
            $classement[$eq['nom']] = ['J' => 0, 'G' => 0, 'N' => 0, 'P' => 0, 'BP' => 0, 'BC' => 0, 'DIFF' => 0, 'PTS' => 0];
        }

        foreach ($matchs as $m) {
            if (is_null($m['score1']) || is_null($m['score2'])) continue;

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
            } elseif ($s1 == $s2) {
                $classement[$e1]['N']++;
                $classement[$e2]['N']++;
                $classement[$e1]['PTS']++;
                $classement[$e2]['PTS']++;
            } else {
                $classement[$e2]['G']++;
                $classement[$e2]['PTS'] += 3;
                $classement[$e1]['P']++;
            }
        }

        foreach ($classement as &$stat) {
            $stat['DIFF'] = $stat['BP'] - $stat['BC'];
        }

        uasort($classement, fn($a, $b) => [$b['PTS'], $b['DIFF'], $b['BP']] <=> [$a['PTS'], $a['DIFF'], $a['BP']]);

        echo "<h3 class='mt-6 mb-2 font-semibold'>Classement</h3>
        <table class='w-full table-auto border border-collapse text-sm'>
            <thead class='bg-blue-900 text-white'>
                <tr>
                    <th class='border px-2'>#</th>
                    <th class='border px-2 text-left'>Équipe</th>
                    <th class='border px-2'>J</th>
                    <th class='border px-2'>G</th>
                    <th class='border px-2'>N</th>
                    <th class='border px-2'>P</th>
                    <th class='border px-2'>BP</th>
                    <th class='border px-2'>BC</th>
                    <th class='border px-2'>+/-</th>
                    <th class='border px-2'>Pts</th>
                </tr>
            </thead><tbody>";

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
        echo "</tbody></table>";
        ?>
    </section>
<?php endforeach; ?>

<?php
// Calendrier général
$matchs = $pdo->query("
    SELECT m.id, m.date_match AS date_heure, m.score1, m.score2, 
           p.nom AS poule, 
           e1.nom AS equipe1, 
           e2.nom AS equipe2
    FROM matchs m
    JOIN poules p ON m.poule_id = p.id
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    ORDER BY m.date_match ASC
")->fetchAll();
?>

<div class="max-w-5xl mx-auto mt-12 p-6 bg-white rounded shadow">
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
</div>

<!-- Modal joueurs -->
<!-- Modal joueurs avec terrain -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg p-4 md:p-6 max-w-3xl w-full relative shadow-lg">
    <button onclick="closeModal()" class="absolute top-2 right-3 text-gray-500 hover:text-gray-900 text-3xl font-bold">&times;</button>
    <h2 id="modal-title" class="text-xl font-bold mb-4 text-center"></h2>

    <div class="relative w-full h-[500px] bg-green-100 border rounded overflow-hidden">
<img src="assets/images/Football.png" alt="Demi-terrain" class="w-full h-full object-cover">

      <div id="player-positions" class="absolute inset-0">
        <!-- les joueurs s'afficheront ici -->
      </div>
    </div>
  </div>
</div>


<script>
function openModal(equipeId, equipeNom) {
    document.getElementById('modal-title').textContent = 'Composition de ' + equipeNom;
    const container = document.getElementById('player-positions');
    container.innerHTML = '<p class="text-white text-center mt-20">Chargement...</p>';

    fetch('get_players.php?equipe_id=' + equipeId)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<p class="text-red-600 text-center mt-20">Aucun joueur trouvé.</p>';
                return;
            }

            const postesFixes = {
                'Gardien': { top: '80%', left: '45%' },
                'Défenseur': [
                    { top: '65%', left: '20%' }, { top: '65%', left: '40%' },
                    { top: '65%', left: '60%' }, { top: '65%', left: '80%' }
                ],
                'Milieu': [
                    { top: '45%', left: '25%' }, { top: '45%', left: '50%' }, { top: '45%', left: '75%' }
                ],
                'Attaquant': [
                    { top: '25%', left: '35%' }, { top: '25%', left: '65%' }
                ]
            };

            let count = {
                'Défenseur': 0,
                'Milieu': 0,
                'Attaquant': 0
            };

            data.forEach((joueur, i) => {
                const div = document.createElement('div');
                div.className = 'absolute bg-blue-600 text-white text-xs px-2 py-1 rounded shadow';

                let top = '50%', left = (10 + i * 10) + '%'; // par défaut

                if (joueur.poste === 'Gardien') {
                    top = postesFixes['Gardien'].top;
                    left = postesFixes['Gardien'].left;
                } else if (postesFixes[joueur.poste] && postesFixes[joueur.poste][count[joueur.poste]]) {
                    const pos = postesFixes[joueur.poste][count[joueur.poste]];
                    top = pos.top;
                    left = pos.left;
                    count[joueur.poste]++;
                } else {
                    // Si poste inconnu ou plus de place, placer aléatoirement
                    top = Math.floor(Math.random() * 70 + 10) + '%';
                    left = Math.floor(Math.random() * 80 + 10) + '%';
                }

                div.style.top = top;
                div.style.left = left;
                div.innerText = joueur.nom + '\n' + (joueur.poste || 'Inconnu');
                container.appendChild(div);
            });
        })
        .catch(() => {
            container.innerHTML = '<p class="text-red-600 text-center mt-20">Erreur chargement joueurs.</p>';
        });

    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modal').classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>


<?php include('includes/footer.php'); ?>
