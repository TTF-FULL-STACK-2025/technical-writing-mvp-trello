<?php
require 'config.php'; 
require_once 'permissions.php'; // NUOVO: Includi il file permessi
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cardId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID Scheda mancante o metodo non valido.']);
    exit;
}

$cardId = (int)$_POST['cardId'];
$user_id = (int)$_SESSION['user_id'];

try {
    // 0. OTTENERE IL BOARD ID dalla CARD ID
    $stmt_board_id = $pdo->prepare("SELECT l.board_id FROM cards c JOIN lists l ON c.list_id = l.list_id WHERE c.card_id = ?");
    $stmt_board_id->execute([$cardId]);
    $board_info = $stmt_board_id->fetch();
    
    if (!$board_info) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Scheda non trovata.']);
        exit;
    }
    $board_id = (int)$board_info['board_id'];

    // 1. VERIFICA DEI PERMESSI (Richiesto: Editor)
    $role = get_user_role_on_board($pdo, $user_id, $board_id);
    if (!is_authorized($role, 'editor')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Non autorizzato ad eliminare questa scheda. Ruolo attuale: ' . ($role ?? 'Non membro')]);
        exit;
    }

    // 2. Esegui DELETE (Logica esistente)
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