<?php 
include 'includes/header.php'; 
require 'includes/config.php'; // <-- connexion PDO $pdo

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Soirée - ISI Festivités</title>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
<style>
  body { font-family: 'Segoe UI', sans-serif; background: #f9f9f9; color: #080020; margin: 0; padding: 20px; }
  h1 { color: rgb(186, 40, 30); }
  #gallery { display: flex; flex-wrap: wrap; gap: 15px; }
  .image-card { background: white; border-radius: 8px; padding: 10px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); width: 200px; }
  .image-card img { max-width: 100%; border-radius: 6px; display: block; margin-bottom: 8px; }
  .image-card p { font-size: 0.9em; color: #333; }

  form { margin-top: 30px; max-width: 400px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
  form label { display: block; margin-bottom: 8px; font-weight: bold; }
  form input[type="file"], form textarea { width: 100%; margin-bottom: 15px; padding: 8px; border-radius: 6px; border: 1px solid #ccc; }
  form button { background: rgb(186, 40, 30); color: white; border: none; padding: 12px 20px; border-radius: 30px; cursor: pointer; font-weight: bold; }
  form button:hover { background: #a02822; }
  #uploadStatus { margin-top: 10px; font-weight: bold; }

  @media (max-width: 600px) {
    #gallery { justify-content: center; }
  }
</style>
</head>
<body>

<h1>🎤 Galerie Soirée</h1>

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

<form id="uploadForm" enctype="multipart/form-data" method="post" action="upload_image.php">
  <label for="image">Ajouter une image :</label>
  <input type="file" name="image" id="image" accept="image/*" required />
  
  <label for="commentaire">Commentaire (optionnel) :</label>
  <textarea name="commentaire" id="commentaire" rows="3" placeholder="Votre commentaire..."></textarea>
  
  <button type="submit">Envoyer</button>
  <div id="uploadStatus"></div>
</form>

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

<?php include 'includes/footer.php'; ?>
</body>
</html>
