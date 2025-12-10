<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<?php
// START DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// END DEBUGGING
require 'config.php'; // Includi il file di connessione
require 'auth.php'; // File di auth
// =======================================================
// INIZIALIZZAZIONE INTERNAZIONALIZZAZIONE (i18n) - DINAMICA
// =======================================================
check_auth();
$user_id = $_SESSION['user_id'];

// 1. SCANSIONA LA CARTELLA LANG PER OTTENERE LE LINGUE DISPONIBILI
$lang_dir = __DIR__ . '/lang/';
$available_langs = [];

// Scansiona tutti i file *.php nella cartella lang/
foreach (glob($lang_dir . '*.php') as $file_path) {
    // Estrai il nome del file senza estensione (es. 'it' da 'it.php')
    $lang_code = basename($file_path, '.php');
    $available_langs[] = $lang_code;
}

$default_lang = 'it';

// 2. Determina la lingua corrente:
$current_lang = $default_lang;

// Usa l'array dinamico $available_langs
if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $current_lang = $_GET['lang'];
    $_SESSION['lang'] = $current_lang;
} elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $available_langs)) {
    $current_lang = $_SESSION['lang'];
}

// 3. Carica il file di configurazione della lingua
$lang_file = __DIR__ . "/lang/{$current_lang}.php";

if (file_exists($lang_file)) {
    $lang = require $lang_file;
} else {
    // Carica il default se il file non esiste (o se il codice è stato rimosso)
    $lang = require __DIR__ . "/lang/{$default_lang}.php";
    $current_lang = $default_lang;
}
?>



<?php
// =======================================================


// =======================================================
// GESTIONE BACHECA (BOARD)
// =======================================================

// 1. Recupera TUTTE le bacheche dell'utente
// CAMBIO CHIAVE: JOIN con board_members per recuperare solo le bacheche dove l'utente è membro.
$stmt_boards = $pdo->prepare("
    SELECT b.board_id, b.name 
    FROM boards b
    JOIN board_members bm ON b.board_id = bm.board_id
    WHERE bm.user_id = ?
    ORDER BY b.board_id ASC
");
$stmt_boards->execute([$user_id]);
$boards = $stmt_boards->fetchAll();

// 2. Determina la bacheca corrente
$current_board_id = null;
$current_board_name = $lang['no_boards'] ?? 'Nessuna Bacheca Trovata'; // Default
$lists = []; // Default

if (!empty($boards)) {
    // --- INIZIO GESTIONE BORAD ID SICURA ---
    $first_board_id = $boards[0]['board_id'];
    $valid_board_ids = array_column($boards, 'board_id');
    
    // Logica di selezione: 1. GET, 2. SESSION, 3. Primo ID
    $potential_id = null;
    if (isset($_GET['board_id']) && is_numeric($_GET['board_id'])) {
        $potential_id = (int)$_GET['board_id'];
    } elseif (isset($_SESSION['current_board_id'])) {
        $potential_id = (int)$_SESSION['current_board_id'];
    } else {
        $potential_id = $first_board_id;
    }

    // Verifica se l'ID potenziale è valido per l'utente, altrimenti usa il primo
    if (in_array($potential_id, $valid_board_ids)) {
        $current_board_id = $potential_id;
    } else {
        $current_board_id = $first_board_id;
    }
    
    $_SESSION['current_board_id'] = $current_board_id; // Imposta o aggiorna la sessione
    // --- FINE GESTIONE BORAD ID SICURA ---


    // 3. Recupera tutte le liste ORA filtrate per board_id
    $stmt_lists = $pdo->prepare("SELECT * FROM lists WHERE board_id = ? ORDER BY position ASC");
    $stmt_lists->execute([$current_board_id]);
    $lists = $stmt_lists->fetchAll();
    
    // Trova il nome della bacheca corrente per il titolo
    foreach($boards as $board) {
        if ($board['board_id'] == $current_board_id) {
            $current_board_name = $board['name'];
            break;
        }
    }
}


// =======================================================
// RECUPERO RUOLO PER IL FRONTEND
// =======================================================

$current_user_role = null; 

if ($current_board_id) {
    // La connessione $pdo e $user_id sono già disponibili qui.
    // Dobbiamo usare la funzione di `permissions.php`
    require_once 'permissions.php'; // Assicurati che permissions.php sia incluso
    $current_user_role = get_user_role_on_board($pdo, $user_id, $current_board_id);
}

// Mappatura dei permessi per il frontend (più semplice):
$can_edit = ($current_user_role == 'owner' || $current_user_role == 'editor');
$can_view = ($current_user_role != null); // Se l'utente è un membro (owner, editor o viewer)
?>


<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['page_title']; ?></title>
    <style>
/* RESET E BASE */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Font moderno */
    margin: 0;
    padding: 30px;
    background-color: #f7f9fc; /* Sfondo molto chiaro */
    color: #1c1f26;
    line-height: 1.5;
}

h1 {
    color: #3b82f6; /* Blu primario per il titolo */
    font-size: 28px;
    margin-bottom: 25px;
}

/* 1. BORDO DELLA BACHECA (BOARD) */
.board {
    display: flex;
    gap: 20px; /* Spazio maggiore tra le liste */
    overflow-x: auto;
    padding-bottom: 20px;
}

/* 2. LISTE (LIST) */
.list {
    flex-shrink: 0;
    width: 320px; /* Leggermente più larga */
    background-color: #ffffff; /* Sfondo lista bianco puro */
    border-radius: 8px; /* Bordi più arrotondati */
    padding: 15px;
    /* Ombra leggera e moderna, non troppo scura */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); 
    transition: box-shadow 0.3s;
}

.list:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12); /* Leggera elevazione all'hover */
}

.list-header h3 {
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 15px 0;
    color: #1c1f26;
    padding-bottom: 5px;
    border-bottom: 1px solid #e2e8f0; /* Separatore leggero */
}

/* 3. SCHEDE (CARD) */
.card-list {
    min-height: 5px; 
    padding-top: 5px;
}

.card {
    background-color: #ffffff;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    /* Ombra discreta per far emergere la scheda */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0; /* Bordo sottile */
    cursor: pointer; /* Cambiato da grab a pointer */
    transition: all 0.2s ease-in-out;
    user-select: none;
    word-wrap: break-word; /* Importante per titoli lunghi */
}

.card:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card small {
    display: block; 
    color: #64748b; /* Grigio più scuro e leggibile */
    font-size: 13px;
    margin-top: 6px;
    opacity: 0.9;
}

/* DRAG AND DROP */
.card.dragging {
    opacity: 0.4;
    transform: rotate(1deg); /* Piccolo effetto visivo di "sollevamento" */
}

.card-list.drag-over {
    background-color: #e0e7ff; /* Sfondo blu molto chiaro */
    border-radius: 8px;
    border: 2px dashed #93c5fd;
}

/* 4. FORM AGGIUNGI SCHEDA */
.add-card-container {
    margin-top: 15px;
}

.add-card-button {
    width: 100%;
    text-align: left;
    padding: 10px;
    background-color: #e2e8f0;
    border: none;
    border-radius: 6px;
    color: #64748b;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.2s;
}
.add-card-button:hover {
    background-color: #cbd5e1;
    color: #1c1f26;
}

.add-card-form {
    padding: 10px 0 0;
    background-color: #f7f9fc;
    border-radius: 6px;
}

.card-title-input,
.card-description-input {
    width: 100%;
    border: 1px solid #a8b0bd;
    border-radius: 4px;
    padding: 8px;
    box-sizing: border-box;
    margin-bottom: 8px;
    resize: vertical;
    font-size: 14px;
}

.card-title-input {
    font-weight: 600;
}

.add-card-form button {
    padding: 8px 15px;
    margin-right: 5px;
    cursor: pointer;
    font-weight: 600;
    border-radius: 4px;
    border: none;
    transition: background-color 0.2s;
}

.cancel-card-button {
    background-color: #e2e8f0;
    color: #475569;
}
.cancel-card-button:hover {
    background-color: #cbd5e1;
}

/* 5. MODALE (CARD DETAIL) */

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4); /* Ombra più leggera */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-overlay.hidden {
    display: none !important; 
}


.modal-content {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 650px; /* Leggermente più largo */
    position: relative;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-close-button {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 30px;
    font-weight: 300; /* Più leggero */
    cursor: pointer;
    color: #64748b;
}
.modal-close-button:hover {
    color: #1c1f26;
}

#modal-card-title {
    font-size: 24px;
    margin-top: 0;
    margin-bottom: 5px;
    color: #1c1f26;
}

.modal-card-list-name {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 20px;
}
.modal-card-list-name span {
    font-weight: 600;
}

.modal-content hr {
    border: 0;
    height: 1px;
    background: #e2e8f0;
    margin: 15px 0;
}

#modal-card-description {
    font-size: 16px;
    color: #475569;
    white-space: pre-wrap; /* Mantiene la formattazione */
}

/* Azioni Modale */
.delete-button {
    background-color: #ef4444; /* Rosso vibrante e moderno */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 25px;
    font-weight: 600;
    transition: background-color 0.2s;
}
.delete-button:hover {
    background-color: #dc2626;
}

.save-card-button {
    background-color: #3b82f6; /* Blu primario */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 25px;
    font-weight: 600;
    transition: background-color 0.2s;
}
.save-card-button:hover {
    background-color: #2563eb;
}

/* Classe generica di utilità */
.hidden {
    display: none !important; /* Mantenuto per risolvere il tuo problema di visibilità */
}


/* NUOVO CSS PER IL DROPDOWN LINGUA */
.language-dropdown {
    position: absolute;
    top: 20px;
    right: 30px;
    z-index: 10; /* Assicura che sia sopra gli altri elementi */
    /* AGGIUNTA CHIAVE: Usa flexbox per allineare lingua e logout */
    display: flex;
    gap: 10px; /* Spazio tra i due pulsanti */
}

.dropdown-toggle {
    background-color: #3b82f6;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    height: 50px;
}
.dropdown-toggle:hover {
    background-color: #2563eb;
}
.dropdown-toggle::after {
    content: ' ▼'; /* Icona a freccia */
    margin-left: 8px;
    font-size: 12px;
}

.dropdown-menu {
    position: absolute;
    top: 100%; /* Sotto il pulsante */
    left: 0;
    background-color: #ffffff;
    min-width: 150px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    border-radius: 6px;
    overflow: hidden;
    margin-top: 5px;
    display: none; /* Nascosto di default */
}

.dropdown-menu.show {
    display: block;
}

.dropdown-menu a {
    color: #1c1f26;
    padding: 10px 15px;
    text-decoration: none;
    display: block;
    transition: background-color 0.2s;
}

.dropdown-menu a:hover,
.dropdown-menu a.active {
    background-color: #e0e7ff; /* Sfondo chiaro all'hover o se attivo */
    color: #3b82f6;
    font-weight: 600;
}

.logout-button {
    background-color: #ef4444; /* Rosso */
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none; /* Rimuove la sottolineatura */
    display: flex;
    align-items: center;
    transition: background-color 0.2s;
}

.logout-button:hover {
    background-color: #dc2626; /* Rosso più scuro all'hover */
}

/* Contenitore per il solo pulsante lingua e il suo menu, necessario per posizionare il sottomenu */
.lang-toggle-container {
    position: relative; /* Base per il posizionamento assoluto del .dropdown-menu */
}

/* Inserisci questo CSS nel blocco <style> in index.php */

/* Stile per il selettore della bacheca */
.boards-selector-container {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
}

.boards-selector-container h2 {
    margin-right: 20px;
    font-size: 20px;
    color: #1c1f26;
    font-weight: 700;
}

.boards-selector-container select {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #a8b0bd;
    font-size: 14px;
    background-color: #ffffff;
    cursor: pointer;
}

/* Stile per gli elementi disabilitati visualmente */
.disabled-action {
    opacity: 0.5 !important;
    pointer-events: none; /* Disabilita i clic */
    cursor: default !important;
}

/* Stile per la lista membri nel modale Accessi */
#member-list-container {
      max-height: 300px;
      overflow-y: auto;
      margin-bottom: 15px;
      padding-right: 10px; /* Spazio per scrollbar */
}

.member-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid #f1f5f9;
}
.member-row:last-child {
      border-bottom: none;
}
.member-role-select {
      padding: 6px;
      border-radius: 4px;
      border: 1px solid #a8b0bd;
      font-size: 13px;
}
.remove-member-btn {
      background-color: #ef4444;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
}
.member-name {
      font-weight: 600;
}
.owner-tag {
      background-color: #fcd34d;
      color: #78350f;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 700;
}
    </style>
</head>
<body>
    <script>
    const TRANSLATIONS = {
        alert_title_empty: "<?php echo str_replace('"', '\"', $lang['alert_title_empty']); ?>",
        alert_delete_confirm: "<?php echo str_replace('"', '\"', $lang['alert_delete_confirm']); ?>",
        alert_title_new_empty: "<?php echo str_replace('"', '\"', $lang['alert_title_new_empty']); ?>",// NUOVO: Passa i permessi al frontend
        CAN_EDIT: "<?php echo $can_edit ? 'true' : 'false'; ?>"
    };
</script>
<div id="card-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <span class="modal-close-button">&times;</span>
        <div id="modal-card-details">
            <input type="text" id="modal-card-title-input" class="card-title-input" value="">
            
            <p class="modal-card-list-name"><?php echo $lang['in_list']; ?>: <span id="modal-card-list-name"></span></p>
            
            <hr>
            
            <h3><?php echo $lang['description']; ?>:</h3>
            <textarea id="modal-card-description-input" class="card-description-input" rows="5"></textarea>
            
            <div class="modal-actions">
                <button id="save-card-details-button" class="save-card-button" style="background-color: #007bff;"><?php echo $lang['save_changes']; ?></button>
                
                <button id="delete-card-button" class="delete-button"><?php echo $lang['delete_card']; ?></button>
            </div>
        </div>
    </div>
</div>  

<div id="access-modal" class="modal-overlay hidden">
      <div class="modal-content" style="max-width: 500px;">
            <span class="modal-close-button" onclick="closeAccessModal()">&times;</span>
            
            <h3>👥 <?php echo $lang['board_access'] ?? 'Accesso alla Bacheca'; ?>: <span id="access-board-name"></span></h3>
            <hr>
            <div id="member-list-container">
                <p><?php echo $lang['loading_members'] ?? 'Caricamento membri...'; ?></p>
            </div>
            <hr>
            <h4>➕ <?php echo $lang['add_member'] ?? 'Aggiungi un Membro'; ?></h4>
            <input type="text" id="new-member-email" placeholder="<?php echo $lang['email_placeholder'] ?? 'Email dell\'utente...'; ?>" style="width: 70%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #a8b0bd; border-radius: 4px;">
            <select id="new-member-role" style="width: 28%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #a8b0bd; border-radius: 4px;">
                  <option value="editor"><?php echo $lang['role_editor'] ?? 'Editor'; ?></option>
                  <option value="viewer" selected><?php echo $lang['role_viewer'] ?? 'Viewer'; ?></option>
            </select>
            <button onclick="addMember()" class="save-card-button" style="margin-top: 0; background-color: #10b981; width: 100%;">
                  <?php echo $lang['add'] ?? 'Aggiungi'; ?>
            </button>
      </div>
</div>
    
<div class="language-dropdown" id="lang-dropdown">

    <div class="lang-toggle-container">
        <button class="dropdown-toggle" id="dropdown-toggle-button">
            🌐 <?php echo strtoupper($current_lang); ?>
        </button>
        <div class="dropdown-menu" id="dropdown-menu">
            <?php foreach ($available_langs as $code): ?>
                <?php
                    // Cerchiamo la traduzione del nome della lingua, altrimenti usiamo il codice
                    $lang_name_key = 'lang_' . $code;
                    // NOTA: $lang è disponibile qui perché è stata caricata prima nell'inizializzazione PHP
                    $display_name = isset($lang[$lang_name_key]) ? $lang[$lang_name_key] : strtoupper($code);
                ?>
                <a href="?lang=<?php echo $code; ?>" class="<?php echo ($current_lang == $code ? 'active' : ''); ?>">
                    <?php echo htmlspecialchars($display_name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    
    <a href="auth.php?logout=1" class="logout-button">
    🚪 <?php echo $lang['logout'] ?? 'Esci'; ?>
    </a>
</div>

<div class="boards-selector-container">
    <h2><?php echo htmlspecialchars($current_board_name); ?></h2>
    
    <select onchange="window.location.href = 'index.php?board_id=' + this.value">
        <?php if (empty($boards)): ?>
             <option disabled selected><?php echo $lang['no_boards'] ?? 'Nessuna Bacheca Trovata'; ?></option>
        <?php endif; ?>
        <?php foreach ($boards as $board): ?>
            <option 
                value="<?php echo $board['board_id']; ?>"
                <?php echo ($board['board_id'] == $current_board_id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($board['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <button onclick="showNewBoardForm()" style="margin-left: 10px; background-color: #3b82f6; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 600;">
        + <?php echo $lang['new_board_btn'] ?? 'Nuova Bacheca'; ?>
    </button>


<?php if ($current_user_role === 'owner'): ?>
<button onclick="openAccessModal()" 
        style="margin-left: 10px; background-color: #f97316; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 600;">
        🔒 <?php echo $lang['manage_access'] ?? 'Gestisci Accessi'; ?>
</button>
<?php endif; ?>
</div>

<div id="new-board-form-container" class="hidden" style="margin-top: 20px; padding: 15px; border: 1px solid #ccc; background: #fff; max-width: 400px; border-radius: 8px; margin-bottom: 25px;">
    <h3><?php echo $lang['create_new_board'] ?? 'Crea Nuova Bacheca'; ?></h3>
    <input type="text" id="new-board-name" placeholder="<?php echo $lang['board_name_placeholder'] ?? 'Nome della Bacheca...'; ?>" style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #a8b0bd; border-radius: 4px;">
    <button onclick="saveNewBoard()" class="save-card-button" style="margin-top: 0; background-color: #10b981;"><?php echo $lang['create'] ?? 'Crea'; ?></button>
    <button onclick="hideNewBoardForm()" class="cancel-card-button" style="margin-top: 0;"><?php echo $lang['cancel'] ?? 'Annulla'; ?></button>
</div>




    <h1>📋 <?php echo $lang['board_title']; ?></h1>

    <div class="board">
        <?php foreach ($lists as $list): ?>
            <div class="list">
                <div class="list-header">
                    <h3><?php echo htmlspecialchars($list['name']); ?></h3>
                </div>

                <div class="card-list" id="list-<?php echo $list['list_id']; ?>">
                    <?php
                    // 2. Recupera le schede per la lista corrente, ordinate per posizione
                    $stmt_cards = $pdo->prepare("SELECT * FROM cards WHERE list_id = ? ORDER BY position ASC");
                    $stmt_cards->execute([$list['list_id']]);
                    $cards = $stmt_cards->fetchAll();
                    
                    foreach ($cards as $card):
                    ?>
                    <div class="card" draggable="true" data-card-id="<?php echo $card['card_id']; ?>">
                            <?php echo htmlspecialchars($card['title']); ?>
                            <small style="display: block; color: #5e6c84; margin-top: 5px;">
                                <?php echo htmlspecialchars(substr($card['description'], 0, 50)) . '...'; ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="add-card-container">
                    
                <button class="add-card-button <?php echo $can_edit ? '' : 'disabled-action'; ?>" data-list-id="<?php echo $list['list_id']; ?>">
                    <?php echo $lang['add_card']; ?>
                </button>
                    
                    <div class="add-card-form hidden">
    <textarea class="card-title-input" placeholder="<?php echo $lang['card_title_placeholder']; ?>" rows="1"></textarea>
    <textarea class="card-description-input" placeholder="<?php echo $lang['card_desc_placeholder']; ?>" rows="3"></textarea>
        
    <button class="save-card-button" data-list-id="<?php echo $list['list_id']; ?>">
        <?php echo $lang['add']; ?>
    </button>
    <button class="cancel-card-button"><?php echo $lang['cancel']; ?></button>
</div>
                </div>
            </div>
        <?php endforeach; ?>
                    </div>


<script>
    // VARIABILI GLOBALI
    let draggedCardId = null; 
    let currentCardId = null; // ID della card attualmente aperta nel modale
    
    // =======================================================
    // I. INIZIALIZZAZIONE e LISTENERS GENERALI
    // =======================================================

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Assegna i listener (drag and click) a TUTTE le schede esistenti
        document.querySelectorAll('.card').forEach(addCardEventListeners);

        // 2. Assegna gli eventi Drag/Drop a tutte le liste
        document.querySelectorAll('.card-list').forEach(list => {
            list.addEventListener('dragover', handleDragOver);
            list.addEventListener('drop', handleDrop);
            list.addEventListener('dragleave', handleDragLeave);
        });

        // 3. Assegna i listener per il form "Aggiungi Scheda"
        document.querySelectorAll('.add-card-button').forEach(button => {
            button.addEventListener('click', toggleAddCardForm);
        });
        document.querySelectorAll('.cancel-card-button').forEach(button => {
            button.addEventListener('click', toggleAddCardForm);
        });
        document.querySelectorAll('.save-card-button').forEach(button => {
            // Seleziona solo i pulsanti di salvataggio delle nuove schede
            if (!button.id.includes('save-card-details-button')) {
                button.addEventListener('click', saveNewCard);
            }
        });

        // 4. Listener per il Modale (Visualizzazione/Eliminazione/SALVATAGGIO)
        const modal = document.getElementById('card-modal');
        if (modal) {
            modal.querySelector('.modal-close-button').addEventListener('click', closeCardModal);
            modal.addEventListener('click', (e) => {
                // Chiude il modale se si clicca sull'overlay
                if (e.target.id === 'card-modal') {
                    closeCardModal();
                }
            });
            document.getElementById('delete-card-button').addEventListener('click', deleteCardHandler);
            
            // Listener per salvare i dettagli della card
            document.getElementById('save-card-details-button').addEventListener('click', saveCardDetails); 
        
        // NUOVO: Nascondi/disabilita azioni se l'utente è solo Viewer
            if (!TRANSLATIONS.CAN_EDIT) {
                // Nasconde i pulsanti di modifica e cancellazione nel modale
                document.getElementById('save-card-details-button').classList.add('hidden');
                document.getElementById('delete-card-button').classList.add('hidden');
                document.getElementById('save-card-details-button').classList.add('hidden');

                // Disabilita anche gli input field per evitare modifiche accidentali
                document.getElementById('modal-card-title-input').setAttribute('disabled', 'true');
                document.getElementById('modal-card-description-input').setAttribute('disabled', 'true');
            } else {
                 // ABILITA i listener solo se l'utente può editare
                 document.getElementById('delete-card-button').addEventListener('click', deleteCardHandler);
                 document.getElementById('save-card-details-button').addEventListener('click', saveCardDetails); 
            }
        
        
        }
// 5. Listener per il Dropdown Lingua
    const toggleButton = document.getElementById('dropdown-toggle-button');
    const dropdownMenu = document.getElementById('dropdown-menu');

    if (toggleButton && dropdownMenu) { // Questo è un buon check
        toggleButton.addEventListener('click', (e) => {
            e.stopPropagation(); 
            dropdownMenu.classList.toggle('show');
        });

        // Chiudi il menu quando si clicca fuori
        document.addEventListener('click', (e) => {
            if (!dropdownMenu.contains(e.target) && !toggleButton.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
    });

    // Listener per Drag E Click
    function addCardEventListeners(card) {
        // Se si trascina, il click viene interrotto automaticamente
        card.setAttribute('draggable', true);
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('click', openCardModal); // Listener per aprire il modale
    }

    // =======================================================
    // II. DRAG AND DROP LOGIC
    // =======================================================
    
    function handleDragStart(e) {
        const card = e.target;
        draggedCardId = card.dataset.cardId;
        e.dataTransfer.setData('text/plain', draggedCardId);
        e.dataTransfer.effectAllowed = 'move';
        setTimeout(() => card.classList.add('dragging'), 0);
    }

    function handleDragOver(e) {
        e.preventDefault();
        const list = e.currentTarget;
        list.classList.add('drag-over');
        const draggedCard = document.querySelector(`.card[data-card-id="${draggedCardId}"]`);
        if (!draggedCard) return;
        const afterElement = getDragAfterElement(list, e.clientY);
        if (afterElement == null) {
            list.appendChild(draggedCard);
        } else {
            list.insertBefore(draggedCard, afterElement);
        }
    }
    
    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    function handleDrop(e) {
        e.preventDefault();
        const newList = e.currentTarget;
        newList.classList.remove('drag-over');

        if (!draggedCardId) {
            console.error('Nessuna scheda trascinata registrata.');
            return;
        }
        
        const draggedCard = document.querySelector(`.card[data-card-id="${draggedCardId}"]`);
        if (draggedCard) {
            draggedCard.classList.remove('dragging');
        }

        const allCardsInList = Array.from(newList.querySelectorAll('.card'));
        const newPosition = allCardsInList.indexOf(draggedCard) + 1;
        const newListId = newList.id.replace('list-', '');
        const cardId = draggedCardId;

        console.log(`[DROP RICEVUTO] Scheda ID: ${cardId} spostata in Lista ID: ${newListId} alla Posizione: ${newPosition}`);

        updateCardPositionOnServer(cardId, newListId, newPosition);

        draggedCardId = null;
    }
    
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.card:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function updateCardPositionOnServer(cardId, newListId, newPosition) {
        const formData = new FormData();
        formData.append('cardId', cardId);
        formData.append('newListId', newListId);
        formData.append('newPosition', newPosition);

        fetch('update_card_position.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Posizione salvata con successo!', data);
            } else {
                console.error('Errore nel salvataggio della posizione:', data.message);
            }
        })
        .catch(error => {
            console.error('Errore di rete/server:', error);
        });
    }

    // =======================================================
    // III. FUNZIONI: MODALE, SALVATAGGIO E DELETE
    // =======================================================

    function openCardModal(e) {
        const cardElement = e.currentTarget;
        const cardId = cardElement.dataset.cardId;
        const listName = cardElement.closest('.list').querySelector('h3').textContent;  // Nome lista
        
        currentCardId = cardId; 

        // Chiama il backend per ottenere i dettagli completi
        fetch(`get_card_details.php?cardId=${cardId}`)
            .then(response => {
                if (!response.ok) throw new Error('Errore nel recupero dei dettagli.');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const card = data.card;
                    
                    // Popola i CAMPI INPUT del modale per la MODIFICA
                    document.getElementById('modal-card-title-input').value = card.title;
                    document.getElementById('modal-card-description-input').value = card.description || ''; // Usa stringa vuota se null
                    document.getElementById('modal-card-list-name').textContent = listName;
                    
                    // Mostra il modale
                    document.getElementById('card-modal').classList.remove('hidden');
                } else {
                    alert('Errore: Scheda non trovata.');
                }
            })
            .catch(error => {
                console.error('Errore Fetch:', error);
                alert('Impossibile caricare i dettagli della scheda.');
            });
    }

    function closeCardModal() {
        document.getElementById('card-modal').classList.add('hidden');
        currentCardId = null;
    }

    function saveCardDetails() {
        if (!currentCardId) return;

        const newTitle = document.getElementById('modal-card-title-input').value.trim();
        const newDescription = document.getElementById('modal-card-description-input').value.trim();

        if (newTitle === "") {
            alert(TRANSLATIONS.alert_title_empty); // Tradotto
            return;
        }

        const formData = new FormData();
        formData.append('cardId', currentCardId);
        formData.append('title', newTitle);
        formData.append('description', newDescription);

        // Chiamata AJAX a un nuovo file: update_card_details.php
        fetch('update_card_details.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 1. Aggiorna l'elemento Card nel DOM principale
                const cardElement = document.querySelector(`.card[data-card-id="${currentCardId}"]`);
                if (cardElement) {
                    // Aggiorna il titolo (testo principale)
                    // Troviamo il nodo di testo diretto per evitare di cancellare l'elemento <small>
                    let titleNode = Array.from(cardElement.childNodes).find(node => node.nodeType === 3); // Node.TEXT_NODE
                    if (titleNode) {
                        titleNode.nodeValue = newTitle;
                    } else {
                        // Se non esiste, aggiunge il titolo come primo nodo
                        cardElement.prepend(document.createTextNode(newTitle));
                    }
                    
                    // Aggiorna la piccola descrizione
                    const smallElement = cardElement.querySelector('small');
                    if (smallElement) {
                        smallElement.innerHTML = newDescription.substring(0, 50) + '...';
                    } else if (newDescription) {
                         // Se la descrizione è nuova e prima non c'era, la crea
                        const newSmall = document.createElement('small');
                        newSmall.style.cssText = "display: block; color: #5e6c84; margin-top: 5px;";
                        newSmall.innerHTML = newDescription.substring(0, 50) + '...';
                        cardElement.appendChild(newSmall);
                    }
                }
                
                // alert('Scheda aggiornata con successo!'); // Mantenuto come commento, se preferisci un feedback utente
                closeCardModal();
            } else {
                alert('Errore durante il salvataggio: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore di rete/server:', error);
            alert('Impossibile salvare le modifiche.');
        });
    }

    function deleteCardHandler() {
        if (!currentCardId) return;

        if (!confirm(TRANSLATIONS.alert_delete_confirm)) { // Tradotto
            return;
        }

        const cardIdToDelete = currentCardId;

        const formData = new FormData();
        formData.append('cardId', cardIdToDelete);

        fetch('delete_card.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Rimuovi la scheda dal DOM
                const cardElement = document.querySelector(`.card[data-card-id="${cardIdToDelete}"]`);
                if (cardElement) {
                    cardElement.remove();
                }
                
                closeCardModal();
                console.log('Scheda eliminata con successo.');
            } else {
                alert('Errore durante l\'eliminazione: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore di rete/server:', error);
        });
    }

    // =======================================================
    // IV. FUNZIONI AGGIUNGI SCHEDA
    // =======================================================

    function toggleAddCardForm(e) {
        const container = e.target.closest('.add-card-container');
        const form = container.querySelector('.add-card-form');
        const button = container.querySelector('.add-card-button');
        
        form.classList.toggle('hidden');
        button.classList.toggle('hidden');
        
        if (form.classList.contains('hidden')) {
            form.querySelector('.card-title-input').value = '';
            form.querySelector('.card-description-input').value = ''; 
        }
    }

    function saveNewCard(e) {
        const listId = e.target.dataset.listId;
        const container = e.target.closest('.add-card-container');
        
        const titleInput = container.querySelector('.card-title-input');
        const descriptionInput = container.querySelector('.card-description-input');
        
        const title = titleInput.value.trim();
        const description = descriptionInput.value.trim();

        if (title === "") {
            alert(TRANSLATIONS.alert_title_new_empty); // Tradotto
            return;
        }

        const formData = new FormData();
        formData.append('listId', listId);
        formData.append('title', title);
        formData.append('description', description);

        fetch('add_card.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const newCard = document.createElement('div');
                newCard.className = 'card';
                newCard.dataset.cardId = data.card_id;
                
                // Uso il testo di data.title invece di data.title per evitare XSS
                newCard.appendChild(document.createTextNode(data.title));

                if (data.description) {
                    const smallElement = document.createElement('small');
                    smallElement.style.cssText = "display: block; color: #5e6c84; margin-top: 5px;";
                    smallElement.innerHTML = data.description.substring(0, 50) + '...';
                    newCard.appendChild(smallElement);
                }
                
                const cardListContainer = document.getElementById(`list-${listId}`);
                cardListContainer.appendChild(newCard);
                
                addCardEventListeners(newCard); 

                titleInput.value = '';
                descriptionInput.value = '';
                toggleAddCardForm(e); 
            } else {
                alert('Errore durante la creazione della scheda: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore di rete:', error);
        });
    }


    // =======================================================
// V. FUNZIONI: GESTIONE BACHECHE (BOARDS)
// =======================================================

function showNewBoardForm() {
    document.getElementById('new-board-form-container').classList.remove('hidden');
}

function hideNewBoardForm() {
    document.getElementById('new-board-form-container').classList.add('hidden');
    document.getElementById('new-board-name').value = ''; 
}

function saveNewBoard() {
    const boardName = document.getElementById('new-board-name').value.trim();

    if (boardName === "") {
        alert("<?php echo $lang['board_name_placeholder'] ?? 'Inserisci un nome per la bacheca.'; ?>"); 
        return;
    }

    const formData = new FormData();
    formData.append('name', boardName);

    // Chiamata AJAX al file add_board.php
    fetch('add_board.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            // Se la risposta non è OK, lancia un errore per catturarlo nel catch
            return response.json().then(error => { throw new Error(error.message); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Ricarica la pagina per visualizzare la nuova bacheca selezionata
            window.location.href = 'index.php?board_id=' + data.board_id;
        } else {
            alert('Errore durante la creazione della bacheca: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Errore di rete/server:', error);
        alert('Impossibile salvare la nuova bacheca: ' + error.message);
    });
}


// =======================================================
// VI. FUNZIONI: GESTIONE ACCESSI (MEMBERS)
// =======================================================
const CURRENT_BOARD_ID = "<?php echo $current_board_id; ?>";
const CURRENT_USER_ID = "<?php echo $user_id; ?>";
const CURRENT_USER_ROLE = "<?php echo $current_user_role; ?>";
const CAN_EDIT_ACCESS = CURRENT_USER_ROLE === 'owner' || CURRENT_USER_ROLE === 'editor'; // La logica Owner è l'unica che può rimuovere/aggiungere

function openAccessModal() {
      document.getElementById('access-board-name').textContent = "<?php echo htmlspecialchars($current_board_name); ?>";
      document.getElementById('access-modal').classList.remove('hidden');
      loadBoardMembers();
}

function closeAccessModal() {
      document.getElementById('access-modal').classList.add('hidden');
}

function loadBoardMembers() {
      const container = document.getElementById('member-list-container');
      container.innerHTML = '<p><?php echo $lang['loading_members'] ?? 'Caricamento membri...'; ?></p>';

      // Chiama il backend per ottenere l'elenco dei membri
      fetch(`get_board_members.php?board_id=${CURRENT_BOARD_ID}`)
            .then(response => response.json())
            .then(data => {
                  container.innerHTML = '';
                  
                  if (data.success && data.members.length > 0) {
                        data.members.forEach(member => {
                              const memberRow = createMemberRow(member);
                              container.appendChild(memberRow);
                        });
                  } else {
                        container.innerHTML = '<p>Nessun membro trovato. Errore di caricamento o bacheca vuota.</p>';
                  }
            })
            .catch(error => {
                  console.error('Errore nel caricamento membri:', error);
                  container.innerHTML = '<p>Errore di comunicazione con il server.</p>';
            });
}

function createMemberRow(member) {
      const row = document.createElement('div');
      row.className = 'member-row';
      row.dataset.userId = member.user_id;
      row.dataset.role = member.role;

      // 1. Nome/Email
      const info = document.createElement('span');
      info.className = 'member-name';
      info.textContent = member.username;
      row.appendChild(info);

      // 2. Controlli (Select e Remove)
      const controls = document.createElement('div');
      controls.style.display = 'flex';
      controls.style.gap = '10px';
      
      if (member.role === 'owner') {
            const ownerTag = document.createElement('span');
            ownerTag.className = 'owner-tag';
            ownerTag.textContent = '<?php echo $lang['role_owner'] ?? 'Owner'; ?>';
            controls.appendChild(ownerTag);
            // L'Owner non è gestibile, esce dal loop dei controlli
      } else {
            // Select Ruolo
            const select = document.createElement('select');
            select.className = 'member-role-select';
            select.innerHTML = `
                  <option value="editor"><?php echo $lang['role_editor'] ?? 'Editor'; ?></option>
                  <option value="viewer"><?php echo $lang['role_viewer'] ?? 'Viewer'; ?></option>
            `;
            select.value = member.role;
            
            // Disabilita per non-Owner/Editor e per l'Owner stesso se non sta gestendo
            if (!CAN_EDIT_ACCESS) {
                  select.setAttribute('disabled', 'true');
            } else {
                  select.addEventListener('change', (e) => updateMemberRole(member.user_id, e.target.value));
            }

            controls.appendChild(select);

            // Pulsante Rimuovi
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-member-btn';
            removeBtn.textContent = '<?php echo $lang['remove'] ?? 'Rimuovi'; ?>';
            
            if (CAN_EDIT_ACCESS) {
                  removeBtn.addEventListener('click', () => removeMember(member.user_id));
            } else {
                  removeBtn.classList.add('disabled-action');
            }
            
            controls.appendChild(removeBtn);
      }
      
      row.appendChild(controls);
      return row;
}

function updateMemberRole(userId, newRole) {
      const formData = new FormData();
      formData.append('board_id', CURRENT_BOARD_ID);
      formData.append('user_id', userId);
      formData.append('role', newRole);

      fetch('update_member_role.php', {
            method: 'POST',
            body: formData
      })
      .then(response => response.json())
      .then(data => {
            if (data.success) {
                  // Ricarica la lista per riflettere lo stato aggiornato
                  loadBoardMembers(); 
                  if (userId.toString() === CURRENT_USER_ID.toString() && CURRENT_USER_ROLE !== newRole) {
                        // Se l'utente corrente cambia il proprio ruolo, deve ricaricare la pagina per aggiornare i permessi di editing globali
                        alert("Il tuo ruolo è stato modificato. Ricarica la pagina per applicare i nuovi permessi.");
                        window.location.reload();
                  }
            } else {
                  alert('Errore nell\'aggiornamento del ruolo: ' + data.message);
                  loadBoardMembers(); // Ricarica lo stato precedente
            }
      })
      .catch(error => {
            console.error('Errore di rete/server:', error);
            alert('Impossibile aggiornare il ruolo.');
            loadBoardMembers(); 
      });
}

function removeMember(userId) {
      if (userId.toString() === CURRENT_USER_ID.toString()) {
            alert("Non puoi rimuovere te stesso da una bacheca.");
            return;
      }
      
      if (!confirm("<?php echo $lang['alert_confirm_remove'] ?? "Sei sicuro di voler rimuovere questo membro?"; ?>")) {
            return;
      }

      const formData = new FormData();
      formData.append('board_id', CURRENT_BOARD_ID);
      formData.append('user_id', userId);

      fetch('remove_member.php', {
            method: 'POST',
            body: formData
      })
      .then(response => response.json())
      .then(data => {
            if (data.success) {
                  loadBoardMembers(); // Ricarica la lista
            } else {
                  alert('Errore nella rimozione del membro: ' + data.message);
            }
      })
      .catch(error => {
            console.error('Errore di rete/server:', error);
            alert('Impossibile rimuovere il membro.');
      });
}

function addMember() {
      const email = document.getElementById('new-member-email').value.trim();
      const role = document.getElementById('new-member-role').value;

      if (email === "") {
            alert("Inserisci l'email dell'utente.");
            return;
      }

      const formData = new FormData();
      formData.append('board_id', CURRENT_BOARD_ID);
      formData.append('email', email);
      formData.append('role', role);

      fetch('add_member.php', {
            method: 'POST',
            body: formData
      })
      .then(response => response.json())
      .then(data => {
            if (data.success) {
                  document.getElementById('new-member-email').value = '';
                  loadBoardMembers(); // Ricarica la lista
                  alert('Membro aggiunto con successo.');
            } else {
                  alert('Errore nell\'aggiunta del membro: ' + data.message);
            }
      })
      .catch(error => {
            console.error('Errore di rete/server:', error);
            alert('Impossibile aggiungere il membro.');
      });
}
</script>

</body> 
</html>