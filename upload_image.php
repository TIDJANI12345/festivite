 <?php
header('Content-Type: application/json');

try {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Fichier invalide ou manquant.");
    }

    // Vérification du type de fichier
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        throw new Exception("Type de fichier non autorisé.");
    }

    // Générer un nom unique
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $ext;
    $uploadDir = __DIR__ . '/uploads/';
    $uploadPath = $uploadDir . $filename;

    // Créer dossier s'il n'existe pas
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Déplacer le fichier
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        throw new Exception("Erreur lors de l'enregistrement du fichier.");
    }

    // Récupérer le commentaire
    $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : null;

    // Connexion DB
    require_once 'includes/config.php'; // Ajuste le chemin si besoin

    // Insérer dans la base de données
    $stmt = $pdo->prepare("INSERT INTO soiree_images (filename, commentaire, uploaded_at) VALUES (?, ?, NOW())");
    $stmt->execute([$filename, $commentaire]);

    // Réponse
    echo json_encode([
        "success" => true,
        "filename" => $filename,
        "commentaire" => $commentaire
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
