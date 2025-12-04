<?php
require 'config.php'; // Includi il file di connessione al database
require_once 'permissions.php'; // NUOVO: Includi il file permessi
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non valido.']);
    exit;
}

// 1. Recupera i dati POST
$cardId = filter_input(INPUT_POST, 'cardId', FILTER_VALIDATE_INT);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$user_id = (int)$_SESSION['user_id'];

// 2. Validazione
if (!$cardId || empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Dati della scheda non validi o titolo mancante.']);
    exit;
}

try {
    // 0. OTTENERE IL BOARD ID dalla CARD ID
    $stmt_board_id = $pdo->prepare("SELECT l.board_id FROM cards c JOIN lists l ON c.list_id = l.list_id WHERE c.card_id = ?");
    $stmt_board_id->execute([$cardId]);
    $board_info = $stmt_board_id->fetch();
    
    if (!$board_info) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Scheda non trovata o non collegata a una bacheca valida.']);
        exit;
    }
    $board_id = (int)$board_info['board_id'];
    
    // 1. VERIFICA DEI PERMESSI (Richiesto: Editor)
    $role = get_user_role_on_board($pdo, $user_id, $board_id);
    if (!is_authorized($role, 'editor')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Non autorizzato a modificare questa scheda. Ruolo attuale: ' . ($role ?? 'Non membro')]);
        exit;
    }

    // 2. Prepara e esegui l'aggiornamento nel database (Logica esistente)
    $stmt = $pdo->prepare("UPDATE cards SET title = :title, description = :description WHERE card_id = :card_id");
    // ... (Logica di successo esistente) ...
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()]);
}
?>