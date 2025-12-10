<?php
// update_member_role.php
require 'config.php';
require 'auth.php';
require 'permissions.php'; // Includi il file con get_user_role_on_board()

check_auth();
$user_id = $_SESSION['user_id'];
header('Content-Type: application/json');

$board_id = $_POST['board_id'] ?? null;
$target_user_id = $_POST['user_id'] ?? null;
$new_role = $_POST['role'] ?? null;

if (!$board_id || !$target_user_id || !$new_role || !in_array($new_role, ['editor', 'viewer'])) {
    echo json_encode(['success' => false, 'message' => 'Dati non validi.']);
    exit;
}

// 1. Controllo Permessi: solo Owner o Editor possono cambiare i ruoli
$current_user_role = get_user_role_on_board($pdo, $user_id, $board_id);
if ($current_user_role !== 'owner' && $current_user_role !== 'editor') {
    echo json_encode(['success' => false, 'message' => 'Permesso negato: solo Owner/Editor possono aggiornare.']);
    exit;
}

// 2. Controllo: un Owner non può essere declassato
$target_role_check = get_user_role_on_board($pdo, $target_user_id, $board_id);
if ($target_role_check === 'owner') {
    echo json_encode(['success' => false, 'message' => 'Impossibile declassare l\'Owner.']);
    exit;
}

// 3. Esecuzione dell'aggiornamento
$stmt = $pdo->prepare("
    UPDATE board_members 
    SET role = ? 
    WHERE board_id = ? AND user_id = ?
");

try {
    $stmt->execute([$new_role, $board_id, $target_user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Ruolo aggiornato.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Membro non trovato o nessun cambiamento.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()]);
}
?>