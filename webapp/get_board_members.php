<?php
// get_board_members.php
require 'config.php';
require 'auth.php'; 
// Assumi che check_auth() garantisca l'utente sia loggato
check_auth();
$user_id = $_SESSION['user_id'];

header('Content-Type: application/json');

if (!isset($_GET['board_id']) || !is_numeric($_GET['board_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID bacheca non valido.']);
    exit;
}

$board_id = (int)$_GET['board_id'];

// 1. Verifica se l'utente corrente è membro di questa bacheca (sicurezza minima)
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM board_members WHERE board_id = ? AND user_id = ?");
$stmt_check->execute([$board_id, $user_id]);

if ($stmt_check->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'message' => 'Accesso negato o bacheca non trovata.']);
    exit;
}

// 2. Recupera tutti i membri della bacheca, unendo le tabelle per l'email
$stmt_members = $pdo->prepare("
    SELECT bm.user_id, u.username, bm.role
    FROM board_members bm
    JOIN users u ON bm.user_id = u.id
    WHERE bm.board_id = ?
    ORDER BY bm.role = 'owner' DESC, u.username ASC
");

try {
    $stmt_members->execute([$board_id]);
    $members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'members' => $members]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()]);
}
?>