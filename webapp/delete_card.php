<?php
require 'config.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cardId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID Scheda mancante o metodo non valido.']);
    exit;
}

$cardId = (int)$_POST['cardId'];

try {
    $stmt = $pdo->prepare("DELETE FROM cards WHERE card_id = ?");
    $stmt->execute([$cardId]);

    if ($stmt->rowCount()) {
        echo json_encode(['success' => true, 'message' => 'Scheda eliminata con successo.']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Nessuna scheda trovata con quell\'ID.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore nel database: ' . $e->getMessage()]);
}
?>