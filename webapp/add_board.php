<?php
require 'config.php'; 
session_start();
header('Content-Type: application/json');

// Controlla l'autenticazione
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$board_name = trim($_POST['name']);

if (empty($board_name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Il nome della bacheca non può essere vuoto.']);
    exit;
}

try {
    // 1. Avvia la transazione per garantire l'integrità dei dati
    $pdo->beginTransaction();

    // 2. Inserimento nella tabella boards
    $stmt_insert_board = $pdo->prepare("
        INSERT INTO boards (user_id, name) 
        VALUES (?, ?)
    ");
    $stmt_insert_board->execute([$user_id, $board_name]);
    $new_board_id = $pdo->lastInsertId();

    // 3. Inserimento nella tabella board_members (assegnazione come proprietario/membro)
    $stmt_insert_member = $pdo->prepare("
        INSERT INTO board_members (board_id, user_id, role) 
        VALUES (?, ?, 'owner')
    ");
    $stmt_insert_member->execute([$new_board_id, $user_id]);

    // 4. NUOVO: Creazione della lista "Default"
    // NOTA: La posizione (position) è impostata a 1, essendo la prima lista.
    $default_list_name = "Default"; // Puoi sostituire con una variabile di lingua se necessario
    $stmt_insert_list = $pdo->prepare("
        INSERT INTO lists (board_id, name, position) 
        VALUES (?, ?, 1)
    ");
    $stmt_insert_list->execute([$new_board_id, $default_list_name]);


    // 5. Esegui il commit della transazione
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Bacheca e lista predefinita create con successo.',
        'board_id' => $new_board_id
    ]);

} catch (PDOException $e) {
    $pdo->rollBack(); // Annulla tutte e tre le operazioni se una fallisce
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore nel database durante la creazione: ' . $e->getMessage()]);
}
?>