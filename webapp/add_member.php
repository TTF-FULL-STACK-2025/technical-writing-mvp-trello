<?php
// add_member.php
require 'config.php';
require 'auth.php';
require 'permissions.php'; 

check_auth();
$user_id = $_SESSION['user_id'];
header('Content-Type: application/json');

$board_id = $_POST['board_id'] ?? null;
$email = trim($_POST['email'] ?? '');
$role = $_POST['role'] ?? 'viewer';

if (!$board_id || empty($email) || !in_array($role, ['editor', 'viewer'])) {
    echo json_encode(['success' => false, 'message' => 'Dati non validi.']);
    exit;
}

// 1. Controllo Permessi
$current_user_role = get_user_role_on_board($pdo, $user_id, $board_id);
if ($current_user_role !== 'owner' && $current_user_role !== 'editor') {
    echo json_encode(['success' => false, 'message' => 'Permesso negato.']);
    exit;
}

// 2. Trova l'ID utente in base all'email
$stmt_user = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt_user->execute([$email]);
$target_user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$target_user) {
    echo json_encode(['success' => false, 'message' => 'Utente con questo username non trovato.']);
    exit;
}

$target_user_id = $target_user['id'];

// 3. Verifica che l'utente non sia già membro
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM board_members WHERE board_id = ? AND user_id = ?");
$stmt_check->execute([$board_id, $target_user_id]);
if ($stmt_check->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'L\'utente è già un membro di questa bacheca.']);
    exit;
}

// 4. Aggiungi il nuovo membro
$stmt_insert = $pdo->prepare("
    INSERT INTO board_members (board_id, user_id, role) 
    VALUES (?, ?, ?)
");

try {
    $stmt_insert->execute([$board_id, $target_user_id, $role]);
    echo json_encode(['success' => true, 'message' => 'Membro aggiunto.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()]);
}
?>