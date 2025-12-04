<?php
require 'auth.php'; // Include la logica di sessione e autenticazione

// Se l'utente è già loggato, lo reindirizziamo a index.php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    redirect('index.php');
}

// Recupera i messaggi di successo o errore e poi li rimuove dalla sessione
$message = $_SESSION['success'] ?? ($_SESSION['error'] ?? null);
$is_success = isset($_SESSION['success']);
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login / Registrazione</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f9fc; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); width: 350px; }
        h2 { color: #3b82f6; text-align: center; margin-bottom: 25px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; border: none; border-radius: 4px; color: white; cursor: pointer; font-weight: 600; transition: background-color 0.2s; margin-top: 10px; }
        .login-btn { background-color: #3b82f6; }
        .login-btn:hover { background-color: #2563eb; }
        .register-btn { background-color: #10b981; }
        .register-btn:hover { background-color: #059669; }
        .switch-form-btn { background: none; color: #3b82f6; border: none; font-weight: normal; margin-top: 20px; text-align: center; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .success { background-color: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .error { background-color: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .hidden { display: none !important; }
    </style>
</head>
<body>
    <div class="auth-container">
        <?php if ($message): ?>
            <div class="message <?php echo $is_success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form id="login-form" method="POST" action="auth.php" class="<?php echo $is_success ? 'hidden' : ''; ?>">
            <h2>Accedi</h2>
            <input type="text" name="username" placeholder="Nome Utente" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="login-btn">Login</button>
            <button type="button" class="switch-form-btn" onclick="toggleForm('register')">Non hai un account? Registrati</button>
        </form>

        <form id="register-form" method="POST" action="auth.php" class="<?php echo $is_success ? '' : 'hidden'; ?>">
            <h2>Registrazione</h2>
            <input type="text" name="username" placeholder="Nome Utente" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register" class="register-btn">Registrati</button>
            <button type="button" class="switch-form-btn" onclick="toggleForm('login')">Hai già un account? Accedi</button>
        </form>
    </div>

    <script>
        function toggleForm(formType) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            
            if (formType === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
            } else if (formType === 'register') {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
            }
        }
        
        // Se c'è un messaggio di successo, mostra il form di login di default
        <?php if (isset($_SESSION['success'])): ?>
            toggleForm('login');
        <?php elseif (isset($_SESSION['error']) && !empty($_POST['register'])): ?>
            // Se c'è un errore e la richiesta era una registrazione, resta sul form di registrazione
            toggleForm('register');
        <?php endif; ?>
    </script>
</body>
</html>