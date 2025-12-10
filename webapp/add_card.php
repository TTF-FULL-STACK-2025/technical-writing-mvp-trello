<?php
require 'config.php'; 
require_once 'permissions.php'; // Contiene get_user_role_on_board() e is_authorized()
session_start(); // Necessario per accedere a $_SESSION['user_id']
header('Content-Type: application/json');

// --- 1. Controlli Iniziali e Autenticazione ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato. Utente non loggato.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['listId'], $_POST['title'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti.']);
    exit;
}

$listId = (int)$_POST['listId'];
$title = trim($_POST['title']);
$description = isset($_POST['description']) ? trim($_POST['description']) : ''; 
$user_id = (int)$_SESSION['user_id']; // ID dell'utente loggato

if (empty($title)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Il titolo non può essere vuoto.']);
    exit;
}

try {
    // --- 2. Recupero del Board ID ---
    // Dobbiamo trovare a quale bacheca appartiene questa lista per controllare i permessi.
    $stmt_board_id = $pdo->prepare("SELECT board_id FROM lists WHERE list_id = ?");
    $stmt_board_id->execute([$listId]);
    $board_info = $stmt_board_id->fetch();
    
    if (!$board_info) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Lista non trovata.']);
        exit;
    }
    $board_id = (int)$board_info['board_id'];

    // --- 3. Verifica dei Permessi (Richiesto: Editor o Owner) ---
    $role = get_user_role_on_board($pdo, $user_id, $board_id);
    
    if (!is_authorized($role, 'editor')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Non autorizzato a creare schede in questa bacheca. Ruolo attuale: ' . ($role ?? 'Non membro')]);
        exit;
    }
    
    // --- 4. Logica di Inserimento (Logica esistente) ---

    // Determina la prossima posizione
    $stmt_max_pos = $pdo->prepare("SELECT MAX(position) AS max_pos FROM cards WHERE list_id = ?");
    $stmt_max_pos->execute([$listId]);
    $max_pos = $stmt_max_pos->fetchColumn();
    $new_position = ($max_pos === false || $max_pos === null) ? 1 : $max_pos + 1;

    // Aggiornamento dell'INSERT per includere il campo description
    $stmt_insert = $pdo->prepare("
        INSERT INTO cards (list_id, title, description, position) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt_insert->execute([$listId, $title, $description, $new_position]);
    
    $new_card_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Scheda creata con successo.',
        'card_id' => $new_card_id,
        'title' => htmlspecialchars($title),
        'description' => htmlspecialchars($description) 
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore nel database: ' . $e->getMessage()]);
}
?>