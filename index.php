<?php include 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Festivités ISI - Accueil</title>

  <!-- AOS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    * { box-sizing: border-box; scroll-behavior: smooth; }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f9f9f9;
      color: rgb(8, 0, 32);
    }

    /* Loader */
    #loader {
      position: fixed;
      inset: 0;
      background: white;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .spinner {
      width: 50px; height: 50px;
      border: 6px solid #eee;
      border-top: 6px solid rgb(186, 40, 30);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 10px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    body.loaded #loader { display: none; }
    body:not(.loaded) *:not(#loader) { visibility: hidden; }

    /* Header */
    header {
      background: rgb(186, 40, 30);
      color: white;
      text-align: center;
      padding: 60px 20px;
    }
    header h1 {
      font-size: 2.6em;
      margin: 0;
      animation: fadeInDown 1s ease-in-out;
    }
    .icon-spin {
      display: inline-block;
      animation: rotateIcon 5s linear infinite;
    }
    @keyframes rotateIcon {
      0% {transform: rotate(0);} 
      100% {transform: rotate(360deg);}
    }
    header p {
      font-size: 1.1em;
      max-width: 700px;
      margin: 15px auto 25px;
      animation: fadeInUp 1s ease-in-out;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: white;
      color: rgb(8, 0, 32);
      padding: 12px 24px;
      border-radius: 30px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease-in-out;
    }
    .btn:hover {
      background: rgb(255, 235, 235);
      transform: translateY(-2px);
    }

    .section {
      padding: 40px 20px;
      max-width: 1200px;
      margin: auto;
    }

    /* Activités */
    .cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .card {
      flex: 1 1 280px;
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.3s;
    }
    .card:hover { transform: scale(1.02); }
    .card h3 {
      color: rgb(186, 40, 30);
      margin-top: 0;
      transition: transform 0.3s;
    }

    .timer {
      margin-top: 10px;
      font-weight: bold;
      color: rgb(8, 0, 32);
    }

    /* Programme */
    .timeline {
      border-left: 4px solid rgb(186, 40, 30);
      padding-left: 20px;
    }
    .timeline-entry {
      margin-bottom: 20px;
      position: relative;
    }
    .timeline-entry::before {
      content: "\2022";
      position: absolute;
      left: -12px;
      color: rgb(186, 40, 30);
      font-size: 24px;
    }

    /* Président */
    .president {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      align-items: center;
    }
    .president img {
      width: 220px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .president div { flex: 1; min-width: 250px; }

    /* Effet "flash" */
    .flash {
      animation: flash 1s infinite;
      color: #e91e63;
    }
    @keyframes flash {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.4; }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .cards { flex-direction: column; }
      .president { flex-direction: column; text-align: center; }
    }
  </style>
</head>
<body>

<div id="loader">
  <div class="spinner"></div>
  <p>Chargement...</p>
</div>

<header data-aos="fade-down">
  <h1><span class="icon-spin">🎓</span> Bienvenue sur ISSPT Festivités</h1>
  <p>Suivez en direct nos activités : tournoi de football, sortie à Grand-Popo et soirée de clôture.</p>
  <a class="btn" href="#activites" data-aos="zoom-in" data-aos-delay="300">Découvrir les activités</a>
</header>

<section class="section" id="activites" data-aos="fade-up">
  <h2 data-aos="fade-up">🌟 Nos Activités</h2>
  <div class="cards">
    <div class="card" data-aos="zoom-in">
      <h3>⚽ Tournoi de Football</h3>
      <p>Matchs, scores en temps réel, classement et trophée !</p>
      <div class="timer" id="football-timer">⏳ Chargement...</div>
      <a class="btn" href="foot.php">Voir le tournoi</a>
    </div>
    <div class="card" data-aos="zoom-in" data-aos-delay="150">
      <h3>🚌 Sortie à Grand-Popo</h3>
      <p>Détente, découverte et souvenirs inoubliables au bord de la mer.</p>
      <div class="timer" id="sortie-timer">⏳ Chargement...</div>
      <a class="btn" href="sortie.php">Voir les infos</a>
    </div>
    <div class="card" data-aos="zoom-in" data-aos-delay="300">
      <h3>🎤 Soirée de Clôture</h3>
      <p>Animations, spectacles, danses et clôture festive de l’année.</p>
      <div class="timer" id="soiree-timer">⏳ Chargement...</div>
      <a class="btn" href="soiree.php">Voir le programme</a>
    </div>
  </div>
</section>

<section class="section" data-aos="fade-left">
  <h2>📅 Programme</h2>
  <div class="timeline">
    <div class="timeline-entry" data-aos="fade-right">24 Juillet : Finale de Football</div>
    <div class="timeline-entry" data-aos="fade-right" data-aos-delay="100">25 Juillet : Sortie à Grand-Popo</div>
    <div class="timeline-entry" data-aos="fade-right" data-aos-delay="200">26 Juillet : Soirée de Clôture</div>
  </div>
</section>

<section class="section" data-aos="fade-up">
  <h2>👨‍💼 Mot du Président</h2>
  <div class="president">
    <img src="assets/images/president.jpg" alt="Président ISSPT" data-aos="fade-right">
    <div data-aos="fade-left">
      <p>
        Chers camarades, chers invités,<br><br>
        C’est avec une immense fierté que nous vous accueillons sur cette plateforme dédiée à nos festivités. Elle symbolise notre dynamisme, notre créativité et l’esprit de fraternité qui anime notre communauté. Que chaque moment partagé soit un souvenir inoubliable. Merci à toutes celles et ceux qui rendent cela possible !
      </p>
      <p><strong>— Le Président du Comité</strong></p>
    </div>
  </div>
</section>

<section class="section" style="text-align: center;" data-aos="flip-up">
  <a class="btn" href="admin/dashboard.php">👤 Espace Admin</a>
</section>

<script>
function startCountdown(id, dateStr) {
  const timer = document.getElementById(id);
  const eventDate = new Date(dateStr);
  function update() {
    const now = new Date().getTime();
    const diff = eventDate - now;
    if (diff <= 0) {
      timer.innerHTML = "🎉 <span class='flash'>C’est aujourd’hui !</span>";
      return;
    }
    const d = Math.floor(diff / (1000*60*60*24));
    const h = Math.floor((diff / (1000*60*60)) % 24);
    const m = Math.floor((diff / (1000*60)) % 60);
    const s = Math.floor((diff / 1000) % 60);
    timer.innerHTML = `⏳ ${d}j ${h}h ${m}m ${s}s`;
  }
  update();
  setInterval(update, 1000);
}

startCountdown("football-timer", "2025-07-24T08:00:00");
startCountdown("sortie-timer", "2025-07-25T07:30:00");
startCountdown("soiree-timer", "2025-07-26T18:00:00");

window.addEventListener("load", () => {
  setTimeout(() => document.body.classList.add("loaded"), 150);
});
</script>

<!-- AOS Script -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: false,
    mirror: true
  });
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
