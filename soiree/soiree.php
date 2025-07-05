<?php 
include '../includes/header.php'; 
require '../includes/config.php'; // connexion PDO $pdo
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Soirée - ISI Festivités</title>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
 <style>
 * {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', sans-serif;
  background: #f9f9f9;
  color: rgb(8, 0, 32);
  width: 100%;
  overflow-x: hidden;
}

.wrapper {
  max-width: 1200px;
  margin: auto;
  padding: 20px;
  width: 100%;
}

h1, h2 {
  text-align: center;
  color: rgb(186, 40, 30);
  margin: 30px 0;
  font-weight: 800;
}

.banner {
  background: linear-gradient(to right, rgb(186, 40, 30), rgb(130, 20, 20));
  padding: 5px 2px;
  text-align: center;
  border-radius: 0 0 30px 30px;
  width: 100%;
}

.banner h1 {
  font-size: 2.2em;
  animation: fadeInDown 1s ease-in-out;
  color: rgb(29, 29, 29);
}

.infos {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.07);
  font-size: 1.1em;
  line-height: 1.6;
  margin-bottom: 40px;
  width: 100%;
}

.infos p {
  margin-bottom: 12px;
}

.infos span {
  font-weight: bold;
  color: rgb(186, 40, 30);
}

#gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin: 20px 0;
  width: 100%;
}

.image-card {
  background: white;
  border-radius: 10px;
  padding: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.image-card:hover {
  transform: scale(1.02);
}

.image-card img {
  width: 100%;
  border-radius: 8px;
}

form {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  max-width: 450px;
  margin: auto;
  width: 100%;
}

form label {
  font-weight: bold;
  margin-bottom: 6px;
  display: block;
}

form input[type="file"],
form textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

form button {
  background: rgb(186, 40, 30);
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 30px;
  font-weight: bold;
  width: 100%;
  cursor: pointer;
  transition: background 0.3s ease;
}

form button:hover {
  background: rgb(150, 30, 25);
}

#uploadStatus {
  text-align: center;
  margin-top: 10px;
  font-weight: bold;
}

@media screen and (max-width: 768px) {
  .banner h1 {
    font-size: 1.5em;
  }

  .infos {
    font-size: 1em;
  }
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

</style>

</head>
<body>

<div class="banner" data-aos="fade-down">
  <h1>🎉 La Soirée de Clôture</h1>
</div>

<div class="wrapper">
  <div class="infos" data-aos="fade-up">
    <p><span>📅 Date :</span> 26 Juillet 2025</p>
    <p><span>📍 Lieu :</span> Grande Salle ISI, Abomey-Calavi</p>
    <p><span>🕒 Heure :</span> 18h00 – 22h30</p>
    <p><span>🎊 Programme :</span> Danses, animations, remise de prix, photos souvenirs, et ambiance festive !</p>
  </div>

<h2 data-aos="fade-up">🖼️ Galerie des Souvenirs</h2>
<div id="gallery">
    <?php
    $stmt = $pdo->query("SELECT * FROM soiree_images ORDER BY uploaded_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo '<div class="image-card" data-aos="fade-up">';
      echo '<img src="uploads/' . htmlspecialchars($row['filename']) . '" alt="Image soirée">';
      if (!empty($row['commentaire'])) {
        echo '<p>' . nl2br(htmlspecialchars($row['commentaire'])) . '</p>';
      }
      echo '</div>';
    }
    ?>
</div>

<h2 data-aos="fade-up">📤 Partagez vos moments</h2>
<form id="uploadForm" enctype="multipart/form-data" method="post" action="upload_image.php">
    <label for="image">Image :</label>
    <input type="file" name="image" id="image" accept="image/*" required />

    <label for="commentaire">Commentaire :</label>
    <textarea name="commentaire" id="commentaire" rows="3" placeholder="Votre commentaire..."></textarea>

    <button type="submit">Envoyer</button>
    <div id="uploadStatus"></div>
  </form>
</div>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 1000, once: false, mirror: true });

  const form = document.getElementById('uploadForm');
  const statusDiv = document.getElementById('uploadStatus');
  const gallery = document.getElementById('gallery');

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(form);
    statusDiv.style.color = 'black';
    statusDiv.textContent = "Envoi en cours...";
    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData
      });
      const result = await response.json();
      if (result.success) {
        statusDiv.style.color = 'green';
        statusDiv.textContent = "Image ajoutée avec succès !";
        const card = document.createElement('div');
        card.className = 'image-card';
        card.setAttribute('data-aos', 'fade-up');
        card.innerHTML = `
          <img src="uploads/${result.filename}" alt="Image soirée">
          ${result.commentaire ? '<p>' + result.commentaire.replace(/\n/g, '<br>') + '</p>' : ''}
        `;
        gallery.prepend(card);
        AOS.refresh();
        form.reset();
      } else {
        statusDiv.style.color = 'red';
        statusDiv.textContent = "Erreur : " + result.message;
      }
    } catch (err) {
      statusDiv.style.color = 'red';
      statusDiv.textContent = "Erreur réseau ou serveur.";
    }
  });
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>