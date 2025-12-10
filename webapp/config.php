<?php
// Dettagli di connessione al database
$host = 'localhost'; // Se usi XAMPP/WAMP, 'localhost' è l'impostazione predefinita
$db   = 'trello_clone'; // Il nome del database che hai creato
$user = 'root';      // Utente di default per XAMPP/WAMP
$pass = 'IY6L43dX';          // Password di default vuota per XAMPP/WAMP (CAMBIALA IN PRODUZIONE!)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
     // Per il progetto scolastico, puoi usare un semplice:
     // die("Errore di connessione: " . $e->getMessage());
}
?>