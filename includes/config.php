<?php
$host = "localhost";       
$user = "root";            
$password = "root";        
$dbname = "festivite";     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}

