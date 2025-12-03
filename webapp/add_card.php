<?php
require 'config.php'; 

header('Content-Type: application/json');

// Aggiornamento: Controlla anche per la 'description' (anche se può essere vuota)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['listId'], $_POST['title'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti.']);
    exit;
}

$listId = (int)$_POST['listId'];
$title = trim($_POST['title']);
// NOVITÀ: Recupera la descrizione. Usa un valore vuoto se non è stata inviata.
$description = isset($_POST['description']) ? trim($_POST['description']) : ''; 

if (empty($title)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Il titolo non può essere vuoto.']);
    exit;
}

try {
    // 1. Determina la prossima posizione
    $stmt_max_pos = $pdo->prepare("SELECT MAX(position) AS max_pos FROM cards WHERE list_id = ?");
    $stmt_max_pos->execute([$listId]);
    $max_pos = $stmt_max_pos->fetchColumn();
    $new_position = ($max_pos === false || $max_pos === null) ? 1 : $max_pos + 1;

    // 2. Aggiornamento dell'INSERT per includere il campo description
    $stmt_insert = $pdo->prepare("
        INSERT INTO cards (list_id, title, description, position) 
        VALUES (?, ?, ?, ?)
    ");
    // NOVITÀ: Passa la variabile $description nell'esecuzione
    $stmt_insert->execute([$listId, $title, $description, $new_position]);
    
    $new_card_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Scheda creata con successo.',
        'card_id' => $new_card_id,
        'title' => htmlspecialchars($title),
        'description' => htmlspecialchars($description) // Restituiamo anche la descrizione
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore nel database: ' . $e->getMessage()]);
}
?>