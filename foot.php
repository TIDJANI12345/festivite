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
<!-- En-tête foot stylé -->
<div class="relative bg-[rgb(186,40,30)] text-white text-center py-10 px-4 rounded-b-3xl shadow-lg overflow-hidden"
     data-aos="zoom-in" data-aos-duration="1000">

  <!-- Stickers animés -->
  <div class="absolute top-2 left-2 text-4xl animate-bounce">⚽</div>
  <div class="absolute top-2 right-2 text-4xl animate-bounce">🔥</div>

  <h1 class="text-4xl md:text-5xl font-extrabold tracking-wide mb-2">
    Tournoi de Football ISSPT Festivités
  </h1>

  <p style="color: rgb(8, 0, 32);" class="text-lg md:text-xl font-semibold italic"">
    Passion ⚽ | Compétition 🔥 | Fair-play 🤝
  </p>

  <!-- Effet de bordure bas -->
  <div class="absolute bottom-0 left-0 w-full h-2 bg-gradient-to-r from-[#ba281e] via-orange-500 to-[#ba281e] animate-pulse"></div>
</div>


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

    <?php
// Cette section a été revisitée avec un style moderne et dynamique utilisant Tailwind CSS
?>

<section 
  class="max-w-7xl mx-auto mt-12 px-4 py-8 bg-white rounded-2xl shadow-2xl border-l-4 border-[rgb(186,40,30)] animate__animated animate__fadeInUp"
  data-aos="fade-up" data-aos-duration="1000"
>
  <h2 class="text-3xl md:text-4xl font-extrabold text-[rgb(186,40,30)] mb-6 relative pb-2 border-b-4 border-[rgb(8,0,32)] w-fit mx-auto">
    ⚽ <?= strtoupper($poule_nom) ?>
    <span class="absolute left-0 bottom-0 w-10 h-1 bg-[rgb(8,0,32)] animate-pulse"></span>
  </h2>
  <!-- Boutons d’équipes -->
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-10">
    <?php foreach ($equipes as $eq): ?>
      <button
        onclick="openModal(<?= $eq['id'] ?>, '<?= htmlspecialchars(addslashes($eq['nom'])) ?>')"
        class="bg-[rgb(8,0,32)] hover:bg-[rgb(186,40,30)] transition duration-300 ease-in-out text-white font-bold py-2 px-4 rounded-xl shadow-md hover:scale-105"
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
  <h3 class="text-xl font-bold text-gray-800 mb-4 border-l-4 border-[rgb(8,0,32)] pl-3">Matchs</h3>
  <?php foreach ($matchs as $match): ?>
    <div class="bg-gray-50 rounded-lg shadow p-4 mb-4 flex justify-between items-center transition-all duration-300 hover:bg-gray-100">
      <span class="font-semibold text-[rgb(8,0,32)]">
        <?= htmlspecialchars($match['equipe1']) ?>
        <?= is_null($match['score1']) ? '' : $match['score1'] ?>
        -
        <?= is_null($match['score2']) ? '' : $match['score2'] ?>
        <?= htmlspecialchars($match['equipe2']) ?>
      </span>
      <span class="text-sm <?= $match['statut'] === 'terminé' ? 'text-green-600' : 'text-orange-500' ?>">
        <?= $match['statut'] === 'terminé' ? '✅ Terminé' : '⏳ À venir' ?>
      </span>
    </div>
  <?php endforeach; ?>

  <!-- Tableau de classement -->
  <h3 class="mt-10 mb-4 text-xl font-bold text-[rgb(8,0,32)]">Classement</h3>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left border border-collapse">
      <thead class="bg-[rgb(8,0,32)] text-white">
        <tr>
          <th class="border px-3 py-2">#</th>
          <th class="border px-3 py-2">Équipe</th>
          <th class="border px-3 py-2">J</th>
          <th class="border px-3 py-2">G</th>
          <th class="border px-3 py-2">N</th>
          <th class="border px-3 py-2">P</th>
          <th class="border px-3 py-2">BP</th>
          <th class="border px-3 py-2">BC</th>
          <th class="border px-3 py-2">+/-</th>
          <th class="border px-3 py-2">Pts</th>
        </tr>
      </thead>
      <tbody class="text-gray-800">
        <?php
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
 $pos = 1; foreach ($classement as $equipe => $stat): ?>
          <tr class="<?= $pos % 2 === 0 ? 'bg-gray-100' : '' ?> hover:bg-orange-50 transition">
            <td class="border px-3 py-2 text-center font-semibold"><?= $pos ?></td>
            <td class="border px-3 py-2"><?= htmlspecialchars($equipe) ?></td>
            <td class="border px-3 py-2 text-center"><?= $stat['J'] ?></td>
            <td class="border px-3 py-2 text-center"><?= $stat['G'] ?></td>
            <td class="border px-3 py-2 text-center"><?= $stat['N'] ?></td>
            <td class="border px-3 py-2 text-center"><?= $stat['P'] ?></td>
            <td class="border px-3 py-2 text-center"><?= $stat['BP'] ?></td>
            <td class="border px-3 py-2 text-center"><?= $stat['BC'] ?></td>
            <td class="border px-3 py-2 text-center"><?= $stat['DIFF'] ?></td>
            <td class="border px-3 py-2 text-center font-bold text-[rgb(186,40,30)]"><?= $stat['PTS'] ?></td>
          </tr>
        <?php $pos++; endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Animation fade-in CSS -->
<style>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}
</style>

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

<div 
  class="max-w-6xl mx-auto mt-14 px-6 py-8 bg-white rounded-2xl shadow-2xl border-l-4 border-[rgb(186,40,30)] animate__animated animate__fadeInUp"
  data-aos="fade-up" data-aos-duration="1000"
>
  <h2 class="text-3xl md:text-4xl font-extrabold text-[rgb(186,40,30)] mb-6 flex items-center gap-3 border-b-4 border-[rgb(8,0,32)] pb-2 w-fit mx-auto relative">
    📅 Calendrier Général
    <span class="absolute left-0 bottom-0 w-10 h-1 bg-[rgb(8,0,32)] animate-pulse"></span>
  </h2>

  <div class="overflow-x-auto rounded-lg shadow-lg">
    <table class="w-full text-sm text-left border-collapse">
      <thead class="bg-[rgb(8,0,32)] text-white uppercase">
        <tr>
          <th class="px-4 py-3 border border-[rgb(186,40,30)]">Date & Heure</th>
          <th class="px-4 py-3 border border-[rgb(186,40,30)]">Poule</th>
          <th class="px-4 py-3 border border-[rgb(186,40,30)]">Équipe 1</th>
          <th class="px-4 py-3 border border-[rgb(186,40,30)]">Équipe 2</th>
          <th class="px-4 py-3 border border-[rgb(186,40,30)] text-center">Score</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($matchs as $m): ?>
        <tr class="odd:bg-white even:bg-gray-50 hover:bg-[rgb(186,40,30)] hover:text-white transition duration-300 ease-in-out cursor-pointer">
          <td class="border border-[rgb(186,40,30)] px-4 py-2 whitespace-nowrap">
            <?= $m['date_heure'] ? date('d/m/Y H:i', strtotime($m['date_heure'])) : '⏳ À venir' ?>
          </td>
          <td class="border border-[rgb(186,40,30)] px-4 py-2"><?= htmlspecialchars($m['poule']) ?></td>
          <td class="border border-[rgb(186,40,30)] px-4 py-2"><?= htmlspecialchars($m['equipe1']) ?></td>
          <td class="border border-[rgb(186,40,30)] px-4 py-2"><?= htmlspecialchars($m['equipe2']) ?></td>
          <td class="border border-[rgb(186,40,30)] px-4 py-2 text-center font-semibold">
            <?= is_null($m['score1']) || is_null($m['score2']) ? '⏳' : "{$m['score1']} - {$m['score2']}" ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<style>
    @media (max-width: 500px) {
  #player-positions div {
    font-size: 10px;
    padding: 2px 5px;
  }
}

</style>
<!-- Modal joueurs -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 transition-opacity duration-300">
  <div class="bg-white rounded-xl p-6 max-w-3xl w-full relative shadow-2xl animate-fadeIn">
    <button onclick="closeModal()" 
      class="absolute top-3 right-4 text-gray-500 hover:text-[rgb(186,40,30)] text-4xl font-bold transition-colors duration-300">
      &times;
    </button>
    <h2 id="modal-title" class="text-2xl font-bold mb-6 text-center text-[rgb(8,0,32)]"></h2>

    <div class="relative w-full h-[500px] bg-green-100 border border-[rgb(8,0,32)] rounded overflow-hidden shadow-inner">
      <img src="assets/images/Football.png" alt="Demi-terrain" class="w-full h-full object-contain md:object-cover">
      <div id="player-positions" class="absolute inset-0"></div>
    </div>
  </div>
</div>

<style>
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}
.animate-fadeIn {
  animation: fadeIn 0.4s ease forwards;
}
</style>

<script>
function openModal(equipeId, equipeNom) {
  const modal = document.getElementById('modal');
  document.getElementById('modal-title').textContent = 'Composition de ' + equipeNom;
  const container = document.getElementById('player-positions');
  container.innerHTML = '<p class="text-[rgb(8,0,32)] text-center mt-20 font-semibold">Chargement...</p>';

  fetch('get_players.php?equipe_id=' + equipeId)
    .then(res => res.json())
    .then(data => {
    container.innerHTML = '';

    if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = '<p class="text-red-600 text-center mt-20">Aucun joueur trouvé.</p>';
        return;
    }

    const lignes = [1, 3, 3, 3, 4]; // ex: 1-3-3-3-4
const hauteurs = [80, 60, 45, 30, 15]; // top% pour chaque ligne
let ligneActuelle = 0;
let indexDansLigne = 0;
let joueursPlaces = 0;

data.forEach((joueur, i) => {
    const div = document.createElement('div');
    div.className = 'absolute bg-blue-600 text-white text-xs px-2 py-1 rounded shadow text-center';
    div.style.whiteSpace = 'nowrap';
    div.innerHTML = joueur.nom + '<br><span class="text-[10px] italic">' + (joueur.poste || 'Inconnu') + '</span>';

    const nbDansLigne = lignes[ligneActuelle] ?? 3;
    const top = hauteurs[ligneActuelle] ?? 10;

    // positionnement horizontal basé sur index dans la ligne
    let left;
if (nbDansLigne === 1) {
    left = 40; // Gardien un peu à gauche
} else if (nbDansLigne === 2) {
    left = indexDansLigne === 0 ? 0 : 50; // 1er très à gauche, 2e au centre-gauche
} else if (nbDansLigne === 3) {
    left = indexDansLigne === 0 ? 5 : indexDansLigne === 1 ? 40 : 80; // Très à gauche, milieu-gauche, droite modérée
} else if (nbDansLigne === 4) {
    left = [5, 25, 45, 65][indexDansLigne]; // Plus de poids sur la partie gauche
} else {
    left = (100 / (nbDansLigne + 1)) * (indexDansLigne + 1) - 10; // décalage plus marqué à gauche
}


    div.style.top = top + '%';
    div.style.left = left + '%';

    container.appendChild(div);

    indexDansLigne++;
    joueursPlaces++;

    if (indexDansLigne >= nbDansLigne) {
        ligneActuelle++;
        indexDansLigne = 0;
    }
});

})
    .catch(() => {
      container.innerHTML = '<p class="text-red-600 text-center mt-20 font-semibold">Erreur chargement joueurs.</p>';
    });
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeModal() {
  const modal = document.getElementById('modal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}
</script>


<?php include('includes/footer.php'); ?>