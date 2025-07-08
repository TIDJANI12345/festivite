<?php 
include 'includes/header.php'; 
require 'includes/config.php'; // connexion PDO $pdo

// Récupérer les sorties triées par date
$sorties = $pdo->query("SELECT * FROM sorties ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour récupérer les images d'une sortie
function getImagesBySortie($pdo, $sortie_id) {
    $stmt = $pdo->prepare("SELECT * FROM sortie_images WHERE sortie_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$sortie_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

<style>
  body { font-family: 'Segoe UI', sans-serif; background: #f9f9f9; color: #080020; margin: 0; padding: 20px; }
  h1 { color: rgb(186, 40, 30); }

  .gallery {
    display: flex; 
    flex-wrap: wrap; 
    gap: 15px; 
    margin-bottom: 20px;
  }
  .image-card { 
    background: white; 
    border-radius: 8px; 
    padding: 10px; 
    box-shadow: 0 3px 8px rgba(0,0,0,0.1); 
    width: 200px; 
  }
  .image-card img { 
    max-width: 100%; 
    border-radius: 6px; 
    display: block; 
    margin-bottom: 8px; 
  }
  .image-card p { font-size: 0.9em; color: #333; }

  form.uploadForm { 
    margin-top: 15px; 
    max-width: 400px; 
    background: white; 
    padding: 20px; 
    border-radius: 10px; 
    box-shadow: 0 3px 10px rgba(0,0,0,0.1); 
  }
  form.uploadForm label { display: block; margin-bottom: 8px; font-weight: bold; }
  form.uploadForm input[type="file"], form.uploadForm textarea { width: 100%; margin-bottom: 15px; padding: 8px; border-radius: 6px; border: 1px solid #ccc; }
  form.uploadForm button { 
    background: rgb(186, 40, 30); 
    color: white; 
    border: none; 
    padding: 12px 20px; 
    border-radius: 30px; 
    cursor: pointer; 
    font-weight: bold; 
  }
  form.uploadForm button:hover { background: #a02822; }
  .uploadStatus { margin-top: 10px; font-weight: bold; }

  @media (max-width: 600px) {
    .gallery { justify-content: center; }
  }
</style>

<div class="max-w-4xl mx-auto px-4 py-10">
  <section class="text-center mb-12">
    <h1 class="text-4xl font-extrabold text-blue-700 mb-4">Sorties pédagogiques 2025</h1>
    <p class="text-lg text-gray-700 mb-6">Embarquez avec nous pour une aventure éducative inoubliable ! 🚍</p>
    <img src="https://cdn-icons-png.flaticon.com/512/61/61088.png" alt="Bus scolaire" class="mx-auto w-24 mb-6" />
    <p class="text-gray-600 max-w-3xl mx-auto">
      Nos sorties sont conçues pour enrichir vos connaissances tout en découvrant de nouveaux horizons.
      Préparez-vous à explorer des lieux passionnants, à rencontrer des experts et à partager des moments conviviaux entre étudiants et enseignants.
    </p>
  </section>

  <?php if (count($sorties) === 0): ?>
    <p class="text-center text-gray-500 text-xl font-semibold">Aucune sortie pédagogique n'est programmée pour le moment. Restez à l'affût !</p>
  <?php else: ?>
    <div class="space-y-12">
      <?php foreach ($sorties as $sortie): ?>
        <article class="border border-blue-300 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow bg-white" data-aos="fade-up">
          <h2 class="text-2xl font-bold text-blue-800 mb-2"><?= htmlspecialchars($sortie['nom']) ?></h2>
          <div class="text-sm text-blue-600 italic mb-4 flex flex-wrap gap-4">
            <span>📅 <?= date('d/m/Y', strtotime($sortie['date'])) ?></span>
            <span>📍 <?= htmlspecialchars($sortie['lieu']) ?></span>
          </div>
          <p class="text-gray-700 whitespace-pre-line leading-relaxed mb-4"><?= nl2br(htmlspecialchars($sortie['description'])) ?></p>

          <h3 class="mb-2 font-semibold text-lg">Galerie images</h3>
          <div id="gallery-<?= $sortie['id'] ?>" class="gallery">
            <?php 
              $images = getImagesBySortie($pdo, $sortie['id']);
              foreach ($images as $img): ?>
                <div class="image-card" data-aos="fade-up">
                  <img src="uploads/sortie/<?= htmlspecialchars($img['filename']) ?>" alt="Image sortie">
                  <?php if (!empty($img['commentaire'])): ?>
                    <p><?= nl2br(htmlspecialchars($img['commentaire'])) ?></p>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
          </div>

          <form class="uploadForm" enctype="multipart/form-data" method="post" action="galerie_sortie.php">
            <input type="hidden" name="sortie_id" value="<?= $sortie['id'] ?>">
            <label for="image_<?= $sortie['id'] ?>">Ajouter une image :</label>
            <input type="file" name="image" id="image_<?= $sortie['id'] ?>" accept="image/*" required />
            
            <label for="commentaire_<?= $sortie['id'] ?>">Commentaire (optionnel) :</label>
            <textarea name="commentaire" id="commentaire_<?= $sortie['id'] ?>" rows="3" placeholder="Votre commentaire..."></textarea>
            
            <button type="submit">Envoyer</button>
            <div class="uploadStatus"></div>
          </form>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 1000, once: false, mirror: true });

  // Gestion des uploads pour chaque formulaire
  document.querySelectorAll('form.uploadForm').forEach(form => {
    const statusDiv = form.querySelector('.uploadStatus');
    const sortieId = form.querySelector('input[name="sortie_id"]').value;
    const gallery = document.getElementById('gallery-' + sortieId);

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

          // Ajouter la nouvelle image dans la galerie
          const card = document.createElement('div');
          card.className = 'image-card';
          card.setAttribute('data-aos', 'fade-up');
          card.innerHTML = `
            <img src="uploads/sortie/${result.filename}" alt="Image sortie">
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
  });
</script>

<?php include 'includes/footer.php'; ?>
