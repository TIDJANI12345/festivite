<script src="https://cdn.tailwindcss.com"></script>
<script>AOS.init();</script>
<style>
  nav {
    position: sticky;
    top: 0;
    width: 100%;
    background-color: rgb(8, 0, 32);
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }

  nav[data-aos] {
    animation-duration: 1s;
    animation-fill-mode: both;
  }

  .logo {
    font-size: 1.5em;
    font-weight: bold;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .logo .icon-spin {
    display: inline-block;
    animation: rotateIcon 5s linear infinite;
  }

  @keyframes rotateIcon {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* Desktop menu */
  nav ul {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
    gap: 25px;
  }

  nav ul li a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    position: relative;
    transition: color 0.3s;
  }

  nav ul li a::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    height: 2px;
    width: 0%;
    background: #0bbbd6;
    transition: width 0.3s;
  }

  nav ul li a:hover::after {
    width: 100%;
  }

  nav ul li a:hover {
    color: #0bbbd6;
  }

  .admin-btn {
    background-color: #0bbbd6;
    color: #1e40af;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s;
  }

  .admin-btn:hover {
    background-color: white;
  }

  /* Mobile toggle */
  .menu-toggle {
    display: none;
    flex-direction: column;
    gap: 3px;
    cursor: pointer;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 3001;
    width: 28px;
    height: 22px;
    background: rgba(30,64,175,0.95);
    padding: 5px;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .menu-toggle.active {
    background: rgba(186,40,30,0.95);
  }

  .menu-toggle span {
    width: 100%;
    height: 3px;
    background: white;
    border-radius: 2px;
    transition: all 0.3s ease;
  }

  .menu-toggle.active span:nth-child(1) {
    transform: translateY(9.5px) rotate(45deg);
  }
  .menu-toggle.active span:nth-child(2) {
    opacity: 0;
  }
  .menu-toggle.active span:nth-child(3) {
    transform: translateY(-9.5px) rotate(-45deg);
  }

  /* Mobile slide menu */
  .menu-slide {
    position: fixed;
    top: 0;
    right: -80%;
    width: 70%;
    height: 100%;
    background-color: #1e40af;
    color: white;
    display: flex;
    flex-direction: column;
    padding: 30px 20px;
    transition: right 0.4s ease;
    z-index: 2000;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.2);
  }

  .menu-slide a {
    color: white;
    text-decoration: none;
    font-size: 1.2em;
    margin-bottom: 20px;
    transition: color 0.3s;
  }

  .menu-slide a:hover {
    color: #0bbbd6;
  }

  .menu-slide.show {
    right: 0;
  }

  .admin-btn-slide {
    margin-top: auto;
    background-color: #0bbbd6;
    color: #1e40af;
    padding: 10px 16px;
    border-radius: 20px;
    text-align: center;
    font-weight: bold;
    text-decoration: none;
    transition: background 0.3s;
  }

  .admin-btn-slide:hover {
    background: white;
  }

  /* Overlay */
  .overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease;
    z-index: 1500;
  }

  .overlay.show {
    opacity: 1;
    visibility: visible;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .menu-toggle {
      display: flex;
    }

    nav ul, .admin-btn {
      display: none;
    }
  }
</style>

<!-- ✅ NAVBAR animée AOS -->
<nav data-aos="fade-down">
  <a href="index.php" class="logo"><span class="icon-spin">🎓</span> ISSPT Festivités</a>

  <!-- Burger -->
  <div class="menu-toggle" id="menuToggle" onclick="toggleMobileMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <!-- Desktop -->
  <ul>
    <li><a href="index.php">Accueil</a></li>
    <li><a href="foot.php">Football</a></li>
    <li><a href="sortie.php">Sortie</a></li>
    <li><a href="../soiree/soiree.php">Soirée</a></li>
  </ul>

  <a class="admin-btn" href="admin.php">👤 Admin</a>
</nav>

<!-- Mobile slide menu -->
<div class="menu-slide" id="mobileMenu">
  <a href="index.php" onclick="toggleMobileMenu()">🏠 Accueil</a>
  <a href="foot.php" onclick="toggleMobileMenu()">⚽ Football</a>
  <a href="sortie.php" onclick="toggleMobileMenu()">🚌 Sortie</a>
  <a href="../soiree/soiree.php" onclick="toggleMobileMenu()">🎤 Soirée</a>
  <a class="admin-btn-slide" href="admin.php">👤 Espace Admin</a>
</div>

<div class="overlay" id="menuOverlay" onclick="toggleMobileMenu()"></div>

<script>
  function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('menuOverlay');
    const toggle = document.getElementById('menuToggle');
    menu.classList.toggle('show');
    overlay.classList.toggle('show');
    toggle.classList.toggle('active');
  }
</script>
