<?php
require 'config.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['cardId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID Scheda mancante o metodo non valido.']);
    exit;
}

$cardId = (int)$_GET['cardId'];

try {
    $stmt = $pdo->prepare("SELECT card_id, title, description, list_id FROM cards WHERE card_id = ?");
    $stmt->execute([$cardId]);
    $card = $stmt->fetch();

    if ($card) {
        echo json_encode([
            'success' => true,
            'card' => $card
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Scheda non trovata.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore nel database: ' . $e->getMessage()]);
}
?>