<?php
$host = 'localhost';       // ou 127.0.0.1
$dbname = 'festivite';      // nom de ta base
$username = 'root';        // identifiant Wamp/Xampp
$password = 'Collins.Chado03';            // mot de passe (souvent vide en local)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Pour afficher les erreurs en cas de souci
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("💥 Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
