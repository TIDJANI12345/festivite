<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flash Shop</title>
  <link rel="stylesheet" href="/FlashShop/assets/css/index.css">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <style>
    #mobile-menu {
      transition: transform 0.3s ease, opacity 0.3s ease;
      transform: translateX(-100%);
      opacity: 0;
    }
    #mobile-menu.active {
      transform: translateX(0);
      opacity: 1;
    }
    .hamburger {
      width: 24px;
      height: 18px;
      position: relative;
      cursor: pointer;
      display: inline-block;
      transition: transform 0.4s ease;
    }
    .hamburger span {
      position: absolute;
      height: 3px;
      width: 100%;
      background: white;
      border-radius: 2px;
      opacity: 1;
      left: 0;
      transition: all 0.4s ease;
    }
    .hamburger span:nth-child(1) { top: 0; }
    .hamburger span:nth-child(2) { top: 7.5px; }
    .hamburger span:nth-child(3) { top: 15px; }
    .hamburger.active span:nth-child(1) {
      transform: rotate(45deg);
      top: 7.5px;
    }
    .hamburger.active span:nth-child(2) {
      opacity: 0;
    }
    .hamburger.active span:nth-child(3) {
      transform: rotate(-45deg);
      top: 7.5px;
    }
  </style>
</head>
<body>
<header class="bg-gray-800 text-white">
  <div class="max-w-7xl mx-auto flex items-center justify-between py-2 px-4">
    <div class="flex items-center space-x-2">
      <button id="menu-toggle" class="sm:hidden focus:outline-none" aria-label="Toggle menu">
        <div class="hamburger" id="hamburger-icon">
          <span></span><span></span><span></span>
        </div>
      </button>
      <a href="/FlashShop/index.php" class="flex items-center space-x-2">
        <img src="/FlashShop/uploads/logo.jpg" alt="Logo Flash Shop" class="h-11 w-auto rounded-lg" />
        <span class="font-bold text-xl whitespace-nowrap">Le shopping qui va vite</span>
      </a>
    </div>

    <nav class="flex items-center space-x-4">
      <ul id="nav-links" class="hidden sm:flex space-x-6">
        <li><a href="/FlashShop/index.php" class="hover:underline">Accueil</a></li>
        <li><a href="/FlashShop/produits.php" class="hover:underline">Produits</a></li>
        <li><a href="/FlashShop/apropos.php" class="hover:underline">À propos</a></li>
        <li><a href="/FlashShop/contact.php" class="hover:underline">Contact</a></li>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
          <li><a href="/FlashShop/admin/dashboard.php" class="font-bold hover:underline">Dashboard</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user'])): ?>
          <li><a href="/FlashShop/connexion/logout.php" class="hover:underline">Déconnexion</a></li>
        <?php else: ?>
          <li><a href="/FlashShop/connexion/login.php" class="hover:underline">Connexion</a></li>
        <?php endif; ?>
      </ul>

      <?php if (isset($_SESSION['user'])): ?>
        <span class="hidden sm:inline text-sm text-gray-300">Bienvenue, <?= htmlspecialchars($_SESSION['user']['nom']) ?></span>
      <?php endif; ?>
    </nav>
  </div>

  <!-- Menu mobile -->
  <div id="mobile-menu" class="sm:hidden fixed top-0 left-0 h-full w-64 bg-gray-900 bg-opacity-95 p-6 flex flex-col space-y-6 transform -translate-x-full opacity-0 z-50 rounded-r-lg">
    <button id="menu-close" class="self-end text-white focus:outline-none" aria-label="Close menu">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" >
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
    <ul class="flex flex-col space-y-6">
      <li><a href="/FlashShop/index.php" class="block hover:underline">Accueil</a></li>
      <li><a href="/FlashShop/produits.php" class="block hover:underline">Produits</a></li>
      <li><a href="/FlashShop/apropos.php" class="block hover:underline">À propos</a></li>
      <li><a href="/FlashShop/contact.php" class="block hover:underline">Contact</a></li>
      <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
        <li><a href="/FlashShop/admin/dashboard.php" class="font-bold hover:underline">Dashboard</a></li>
      <?php endif; ?>
      <?php if (isset($_SESSION['user'])): ?>
        <li><a href="/FlashShop/connexion/logout.php" class="block hover:underline">Déconnexion</a></li>
        <li class="text-sm text-gray-300">Bienvenue, <?= htmlspecialchars($_SESSION['user']['nom']) ?></li>
      <?php else: ?>
        <li><a href="/FlashShop/connexion/login.php" class="block hover:underline">Connexion</a></li>
      <?php endif; ?>
    </ul>
  </div>
</header>

<script>
  const menuToggle = document.getElementById('menu-toggle');
  const hamburgerIcon = document.getElementById('hamburger-icon');
  const menuClose = document.getElementById('menu-close');
  const mobileMenu = document.getElementById('mobile-menu');

  function openMenu() {
    mobileMenu.classList.add('active');
    hamburgerIcon.classList.add('active');
  }

  function closeMenu() {
    mobileMenu.classList.remove('active');
    hamburgerIcon.classList.remove('active');
  }

  menuToggle.addEventListener('click', () => {
    if(mobileMenu.classList.contains('active')) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  menuClose.addEventListener('click', () => {
    closeMenu();
  });

  document.addEventListener('click', (e) => {
    if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
      closeMenu();
    }
  });
</script>