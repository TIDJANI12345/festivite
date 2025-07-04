<?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=prosperaai;charset=utf8', 'root', 'Collins.Chado03');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
session_start();
$user_id = 1126; // Assurez-vous que l'utilisateur est connecté

// Récupérer les informations de l'utilisateur
$sql = "SELECT investment_balance, balance, robot_id, last_mining_time FROM users WHERE id = ?";
$stmt = $bdd->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}


// Récupérer les informations du robot
$sql_robot = "SELECT taux_profil, quantification FROM robot WHERE id = ?";
$stmt_robot = $bdd->prepare($sql_robot);
$stmt_robot->execute([$user['robot_id']]);
$robot = $stmt_robot->fetch(PDO::FETCH_ASSOC);

if (!$robot) {
    die("Robot introuvable.");
}


// Initialisation des sessions si elles n'existent pas
if (!isset($_SESSION['quantification_restante'])) {
    $_SESSION['quantification_restante'] = $robot['quantification'];
}

if (!isset($_SESSION['reset_time'])) {
    $_SESSION['reset_time'] = null;
}

// Vérifier le temps écoulé depuis le dernier minage
$last_mining_time = !is_null($user['last_mining_time']) ? strtotime($user['last_mining_time']) : 0;
$current_time = time();
$time_limit = 120; // 2 minutes = 120 secondes
$can_click = ($current_time - $last_mining_time) >= $time_limit;

// Vérifier si toutes les quantifications sont épuisées
$mining_limit_reached = $_SESSION['quantification_restante'] <= 0;

if ($mining_limit_reached) {
    if (is_null($_SESSION['reset_time'])) {
        $_SESSION['reset_time'] = time();
    }
    $time_since_limit = time() - $_SESSION['reset_time'];
    $time_remaining_for_message = max(0, 120 - $time_since_limit);
    
    // Après 2 minutes d'attente, afficher le message final
    if ($time_remaining_for_message <= 0) {
        $show_final_message = true;
    } else {
        $show_final_message = false;
    }

    // Réinitialisation après 3 minutes
    if ($time_since_limit >= 180) {
        $_SESSION['quantification_restante'] = $robot['quantification'];
        $_SESSION['reset_time'] = null;
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['startMining'])) {
    $_SESSION['last_click_time'] = time();

}


// Récupérer l'historique des historique
$stmt = $bdd->prepare("SELECT * FROM historique WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Temps restant avant réactivation du bouton
$elapsed_time = time() - $last_mining_time;
$time_remaining = max(0, $time_limit - $elapsed_time);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Execution avec bouton</title>
    <meta http-equiv="refresh" content="120">  <!-- Recharger la page après 120 secondes (2 minutes) -->
</head>
<body>

<h2>Quantifications restantes : <?= $_SESSION['quantification_restante'] ?></h2>

<!-- Formulaire avec bouton -->
<form method="POST" action="">
            <button type="submit" name="startMining" id="actionBtn">
                Démarrer le minage (<?= $_SESSION['quantification_restante'] ?>/<?= $robot['quantification'] ?>)
            </button>

        
</form>
<h3>Historique des historique</h3>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const actionBtn = document.getElementById('actionBtn');
    if (actionBtn) {
        actionBtn.addEventListener('click', (e) => {
            e.preventDefault();
            actionBtn.disabled = true; // Désactiver le bouton après le clic
            actionBtn.innerText = "Attente en cours..."; // Modifier le texte du bouton
            
            let remainingTime = 100;
            const countdownInterval = setInterval(() => {
                remainingTime--;
                console.log("Temps restant :", remainingTime);
                if (remainingTime <= 0) {
                    clearInterval(countdownInterval);
                    fetch('ajout_gain.php')
                        .then(response => response.text())
                        .then(data => {
                            location.reload(); // Rafraîchir la page après exécution
                        })
                        .catch(error => console.error('Erreur:', error));
                }
            }, 1000);
        });
    }
});

    </script>
</body>
</html>