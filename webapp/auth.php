<?php
require 'config.php'; // Usa la tua connessione PDO

session_start();

// Funzione di reindirizzamento semplice
function redirect($url) {
    header("Location: $url");
    exit();
}

// ===================================
// REGISTRAZIONE
// ===================================
if (isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Compila tutti i campi per registrarti.';
        redirect('login_register_form.php');
    }

    try {
        // 1. Controlla se l'utente esiste già
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = 'Questo nome utente è già in uso.';
            redirect('login_register_form.php');
        }

        // 2. Hash della password (Sicurezza)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. Inserimento nel database
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);

        $_SESSION['success'] = 'Registrazione completata! Effettua il login.';
        redirect('login_register_form.php');

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Errore DB durante la registrazione: ' . $e->getMessage();
        redirect('login_register_form.php');
    }
}

// ===================================
// LOGIN
// ===================================
if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Compila tutti i campi per accedere.';
        redirect('login_register_form.php');
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login Riuscito! Imposta le variabili di sessione
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Reindirizza alla pagina principale dell'applicazione (index.php)
            redirect('index.php');
        } else {
            $_SESSION['error'] = 'Credenziali non valide.';
            redirect('login_register_form.php');
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Errore DB durante il login: ' . $e->getMessage();
        redirect('login_register_form.php');
    }
}

// ===================================
// LOGOUT
// ===================================
if (isset($_GET['logout'])) {
    // Distrugge tutte le variabili di sessione
    $_SESSION = array();
    // Invalida anche il cookie di sessione
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    // Infine, distrugge la sessione
    session_destroy();
    redirect('login_register_form.php');
}

// ===================================
// CHECK AUTENTICAZIONE (da includere in pagine protette)
// ===================================
function check_auth() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // Se non autenticato, reindirizza al form di login
        redirect('login_register_form.php');
    }
}
?>