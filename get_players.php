<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($_GET['equipe_id']) || !is_numeric($_GET['equipe_id'])) {
    echo json_encode(['error' => 'Paramètre equipe_id manquant ou invalide']);
    exit;
}

$equipe_id = (int)$_GET['equipe_id'];

include('includes/config.php'); // $pdo défini ici

try {
    $stmt = $pdo->prepare('SELECT nom, poste, id FROM joueurs WHERE equipe_id = ? ORDER BY nom');
    $stmt->execute([$equipe_id]);

    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$joueurs) {
        echo json_encode(['error' => 'Aucun joueur trouvé.']);
        exit;
    }

    echo json_encode($joueurs);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur base de données : ' . $e->getMessage()]);
}
