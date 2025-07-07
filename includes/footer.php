<!-- 🔻 FOOTER -->
<footer>
  <div class="footer-container">
    <nav class="footer-nav" aria-label="Navigation du pied de page">
      <a href="index.php" class="footer-link">🏠 Accueil</a>
      <a href="foot.php" class="footer-link">⚽ Football</a>
      <a href="sortie.php" class="footer-link">🚌 Sortie</a>
      <a href="../soiree/soiree.php" class="footer-link">🎤 Soirée</a>
    </nav>

    <div class="footer-info">
      <p>📍 Institut Supérieur d’Informatique - Bénin</p>
      <p>📧 <a href="mailto:isi.festivites@exemple.com" class="footer-email">isi.festivites@exemple.com</a></p>
      <p>© <?= date('Y') ?> ISI Festivités. Tous droits réservés.</p>
      <p>Fait avec <span class="heart">❤️</span> par les étudiants en informatique</p>
    </div>
  </div>
</footer>

<!-- ✅ FOOTER STYLES -->
<style>
  footer {
    background-color: rgb(8, 0, 32);
    color: white;
    padding: 30px 20px 25px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
  }

  .footer-container {
    max-width: 1100px;
    margin: auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
  }

  .footer-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    flex: 1 1 100px;
  }

  .footer-link {
    color: #0bbbd6;
    font-weight: 600;
    font-size: 1em;
    text-decoration: none;
    position: relative;
    transition: all 0.3s ease;
  }

  .footer-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -4px;
    left: 0;
    background-color: #0bbbd6;
    transition: width 0.3s ease;
  }

  .footer-link:hover,
  .footer-link:focus {
    color: white;
  }

  .footer-link:hover::after,
  .footer-link:focus::after {
    width: 100%;
  }

  .footer-info {
    flex: 1 1 100px;
    text-align: center;
    font-size: 0.92em;
    line-height: 1.5;
    color: #d1d5db;
  }

  .footer-info p {
    margin: 5px 0;
  }

  .footer-email {
    color: #0bbbd6;
    text-decoration: underline;
    transition: color 0.3s ease;
  }

  .footer-email:hover,
  .footer-email:focus {
    color: white;
  }

  .heart {
    color: #e91e63;
    animation: heartbeat 1.5s infinite;
    display: inline-block;
  }

  @keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.3); }
  }

  @media (max-width: 768px) {
    .footer-container {
      flex-direction: column;
      text-align: center;
    }

    .footer-nav {
      justify-content: center;
      gap: 15px;
    }

    .footer-info {
      font-size: 0.85em;
    }
  }
</style>
