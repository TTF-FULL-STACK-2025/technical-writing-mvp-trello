<?php
// remove_member.php
require 'config.php';
require 'auth.php';
require 'permissions.php'; 

check_auth();
$user_id = $_SESSION['user_id'];
header('Content-Type: application/json');

$board_id = $_POST['board_id'] ?? null;
$target_user_id = $_POST['user_id'] ?? null;

if (!$board_id || !$target_user_id) {
    echo json_encode(['success' => false, 'message' => 'ID bacheca o utente non valido.']);
    exit;
}

// 1. Controllo Permessi: solo Owner o Editor possono rimuovere
$current_user_role = get_user_role_on_board($pdo, $user_id, $board_id);
if ($current_user_role !== 'owner' && $current_user_role !== 'editor') {
    echo json_encode(['success' => false, 'message' => 'Permesso negato: solo Owner/Editor possono rimuovere.']);
    exit;
}

// 2. Controllo: l'Owner non può essere rimosso
$target_role_check = get_user_role_on_board($pdo, $target_user_id, $board_id);
if ($target_role_check === 'owner') {
    echo json_encode(['success' => false, 'message' => 'Impossibile rimuovere l\'Owner.']);
    exit;
}

// 3. Esecuzione della rimozione
$stmt = $pdo->prepare("
    DELETE FROM board_members
    WHERE board_id = ? AND user_id = ?
");

try {
    $stmt->execute([$board_id, $target_user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Membro rimosso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Membro non trovato.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()]);
}
?>