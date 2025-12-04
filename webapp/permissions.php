<?php
// Richiede l'accesso alla connessione DB
require_once 'config.php';

/**
 * Controlla l'autorizzazione dell'utente loggato per una specifica bacheca.
 * * @param PDO $pdo L'oggetto di connessione al database.
 * @param int $user_id L'ID dell'utente loggato.
 * @param int $board_id L'ID della bacheca.
 * @return string|null Il ruolo dell'utente ('owner', 'editor', 'viewer') o null se non è membro.
 */
function get_user_role_on_board(PDO $pdo, $user_id, $board_id) {
    $stmt = $pdo->prepare("
        SELECT role 
        FROM board_members 
        WHERE user_id = ? AND board_id = ?
    ");
    $stmt->execute([$user_id, $board_id]);
    $result = $stmt->fetch();
    
    return $result ? $result['role'] : null;
}

/**
 * Verifica se l'utente ha l'autorizzazione minima richiesta.
 *
 * @param string $actual_role Il ruolo attuale dell'utente.
 * @param string $required_level Il livello richiesto ('owner', 'editor', 'viewer').
 * @return bool True se autorizzato, false altrimenti.
 */
function is_authorized($actual_role, $required_level) {
    if (!$actual_role) {
        return false; // Non è membro
    }

    $role_hierarchy = [
        'owner' => 3,
        'editor' => 2,
        'viewer' => 1
    ];

    $actual_level = $role_hierarchy[$actual_role] ?? 0;
    $required_level_val = $role_hierarchy[$required_level] ?? 0;

    return $actual_level >= $required_level_val;
}

// Mappatura delle autorizzazioni:
// owner: può fare tutto (Gestione membri, CRUD completo)
// editor: CRUD completo su List/Card (Create, Read, Update, Delete)
// viewer: solo Read

?>