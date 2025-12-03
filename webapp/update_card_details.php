<?php
require 'config.php'; // Includi il file di connessione al database

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non valido.']);
    exit;
}

// 1. Recupera i dati POST
$cardId = filter_input(INPUT_POST, 'cardId', FILTER_VALIDATE_INT);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

// 2. Validazione
if (!$cardId || empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Dati della scheda non validi o titolo mancante.']);
    exit;
}

try {
    // 3. Prepara e esegui l'aggiornamento nel database
    $stmt = $pdo->prepare("UPDATE cards SET title = :title, description = :description WHERE card_id = :card_id");
    
    $success = $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':card_id' => $cardId
    ]);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Scheda aggiornata con successo.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna riga modificata.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()]);
}
?>