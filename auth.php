<?php
/**
 * InstaContest - Pagina Autenticazione Completa
 * Login + Registrazione + Google OAuth
 * File: /wp-content/themes/instacontest-child/auth.php
 */

// Carica WordPress solo se non è già caricato
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Disabilita tutti i hooks solo se stiamo accedendo direttamente
if (!did_action('wp_loaded')) {
    remove_all_actions('init');
    remove_all_actions('wp_loaded');
    remove_all_actions('wp_head');
    remove_all_actions('wp_footer');
}

// Redirect se già loggato
if (is_user_logged_in()) {
    header('Location: /profilo');
    exit;
}

// Variabili per gestire lo stato
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login'; // 'login' o 'register'
$errors = array();
$success_message = '';

// ========================================
// GESTIONE REGISTRAZIONE
// ========================================
if (isset($_POST['register_submit'])) {
    $nome = sanitize_text_field($_POST['nome']);
    $cognome = sanitize_text_field($_POST['cognome']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $instagram_username = sanitize_text_field($_POST['instagram_username']);
    $squadre_cuore = isset($_POST['squadre_cuore']) ? $_POST['squadre_cuore'] : array();
    
    // Validazioni
    if (empty($nome)) $errors[] = 'Il nome è obbligatorio';
    if (empty($cognome)) $errors[] = 'Il cognome è obbligatorio';
    if (empty($email) || !is_email($email)) $errors[] = 'Email non valida';
    if (empty($password) || strlen($password) < 6) $errors[] = 'Password minimo 6 caratteri';
    if (empty($instagram_username)) $errors[] = 'Username Instagram obbligatorio';
    if (empty($squadre_cuore)) $errors[] = 'Devi selezionare almeno una squadra';
    if (count($squadre_cuore) > 3) $errors[] = 'Massimo 3 squadre';
    
    // Pulisci username Instagram
    $instagram_username = ltrim($instagram_username, '@');
    
    if (email_exists($email)) {
        $errors[] = 'Email già registrata';
    }
    
    // Crea utente se nessun errore
    if (empty($errors)) {
        $user_id = wp_create_user($email, $password, $email);
        
        if (!is_wp_error($user_id)) {
            // Aggiorna dati utente
            wp_update_user(array(
                'ID' => $user_id,
                'first_name' => $nome,
                'last_name' => $cognome,
                'display_name' => $nome . ' ' . $cognome
            ));
            
            // Salva metadati
            update_user_meta($user_id, 'instagram_username', $instagram_username);
            update_user_meta($user_id, 'squadre_cuore', $squadre_cuore);
            update_user_meta($user_id, 'total_points', 0);
            
            // Login automatico
            wp_clear_auth_cookie();
            wp_set_auth_cookie($user_id, true, is_ssl());
            wp_set_current_user($user_id);
            
            // Redirect immediato
            header('Location: /profilo');
            exit;
        } else {
            $errors[] = 'Errore durante la registrazione. Riprova.';
        }
    }
}

// ========================================
// GESTIONE LOGIN
// ========================================
if (isset($_POST['login_submit'])) {
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    if (empty($email)) $errors[] = 'Email obbligatoria';
    if (empty($password)) $errors[] = 'Password obbligatoria';
    
    if (empty($errors)) {
        $user = wp_authenticate($email, $password);
        
        if (!is_wp_error($user)) {
            // Login PURO
            wp_clear_auth_cookie();
            wp_set_auth_cookie($user->ID, $remember, is_ssl());
            wp_set_current_user($user->ID);
            
            // Redirect IMMEDIATO
            header('Location: /profilo');
            exit;
        } else {
            $errors[] = 'Email o password non corretti';
        }
    }
}

// ========================================
// GESTIONE GOOGLE OAUTH (AJAX)
// ========================================
if (isset($_POST['action']) && $_POST['action'] === 'google_oauth') {
    header('Content-Type: application/json');
    
    $google_token = sanitize_text_field($_POST['google_token']);
    
    // Verifica token Google (funzione dal functions.php)
    $user_data = verify_google_token($google_token);
    
    if (!$user_data) {
        echo json_encode(array('success' => false, 'message' => 'Token Google non valido'));
        exit;
    }
    
    // Cerca utente esistente
    $existing_user = get_user_by('email', $user_data['email']);
    
    if ($existing_user) {
        // LOGIN utente esistente
        wp_clear_auth_cookie();
        wp_set_auth_cookie($existing_user->ID, true, is_ssl());
        wp_set_current_user($existing_user->ID);
        
        echo json_encode(array(
            'success' => true,
            'action' => 'login',
            'redirect' => '/profilo'
        ));
    } else {
        // REGISTRAZIONE nuovo utente
        echo json_encode(array(
            'success' => true,
            'action' => 'register',
            'user_data' => $user_data
        ));
    }
    exit;
}

// ========================================
// COMPLETAMENTO REGISTRAZIONE GOOGLE
// ========================================
if (isset($_POST['action']) && $_POST['action'] === 'complete_google_register') {
    header('Content-Type: application/json');
    
    $google_data = json_decode(stripslashes($_POST['google_data']), true);
    $instagram_username = sanitize_text_field($_POST['instagram_username']);
    $squadre_cuore = isset($_POST['squadre_cuore']) ? $_POST['squadre_cuore'] : array();
    
    $errors = array();
    
    if (empty($instagram_username)) $errors[] = 'Username Instagram obbligatorio';
    if (empty($squadre_cuore)) $errors[] = 'Seleziona almeno una squadra';
    if (count($squadre_cuore) > 3) $errors[] = 'Massimo 3 squadre';
    
    $instagram_username = ltrim($instagram_username, '@');
    
    if (!empty($errors)) {
        echo json_encode(array('success' => false, 'errors' => $errors));
        exit;
    }
    
    // Crea utente
    $username = $google_data['email'];
    $password = wp_generate_password(12, false);
    
    $user_id = wp_create_user($username, $password, $google_data['email']);
    
    if (is_wp_error($user_id)) {
        echo json_encode(array('success' => false, 'message' => 'Errore durante la registrazione'));
        exit;
    }
    
    // Aggiorna dati
    wp_update_user(array(
        'ID' => $user_id,
        'first_name' => $google_data['given_name'],
        'last_name' => $google_data['family_name'],
        'display_name' => $google_data['name']
    ));
    
    // Salva metadati
    update_user_meta($user_id, 'google_id', $google_data['google_id']);
    update_user_meta($user_id, 'google_picture', $google_data['picture']);
    update_user_meta($user_id, 'instagram_username', $instagram_username);
    update_user_meta($user_id, 'squadre_cuore', $squadre_cuore);
    update_user_meta($user_id, 'total_points', 0);
    update_user_meta($user_id, 'registration_method', 'google');
    
    // Login automatico
    wp_clear_auth_cookie();
    wp_set_auth_cookie($user_id, true, is_ssl());
    wp_set_current_user($user_id);
    
    echo json_encode(array(
        'success' => true,
        'redirect' => '/profilo'
    ));
    exit;
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mode === 'register' ? 'Registrati' : 'Accedi'; ?> - InstaContest</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <style>
        /* Bottom Navigation Animations */
        #bottom-nav {
            animation: slideUp 0.4s ease-out;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Hover effects for nav items */
        #bottom-nav a {
            transition: all 0.2s ease;
        }
        
        #bottom-nav a:hover {
            transform: scale(1.05);
        }
        
        #bottom-nav a:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="bg-gray-50 pb-20">

<div class="min-h-screen py-6 px-4">
    <div class="max-w-md mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <img src="https://www.instacontest.it/wp-content/uploads/2025/06/Progetto-senza-titolo-52.png" 
                 alt="InstaContest" 
                 class="w-24 h-24 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                <?php echo $mode === 'register' ? 'Registrati su InstaContest' : 'Accedi a InstaContest'; ?>
            </h1>
            <p class="text-gray-600">
                <?php echo $mode === 'register' ? 'Partecipa ai contest e accumula punti!' : 'Bentornato su InstaContest'; ?>
            </p>
        </div>

        <!-- Toggle Login/Register -->
        <div class="flex bg-gray-100 rounded-xl p-1 mb-6">
            <a href="/login" 
               class="flex-1 text-center py-2 rounded-lg transition <?php echo $mode === 'login' ? 'bg-white text-blue-600 font-semibold shadow-sm' : 'text-gray-600'; ?>">
                Accedi
            </a>
            <a href="/register" 
               class="flex-1 text-center py-2 rounded-lg transition <?php echo $mode === 'register' ? 'bg-white text-blue-600 font-semibold shadow-sm' : 'text-gray-600'; ?>">
                Registrati
            </a>
        </div>

        <!-- Messaggi di errore -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fa-solid fa-exclamation-triangle text-red-500 mr-3 mt-0.5"></i>
                    <div>
                        <h3 class="font-bold text-red-800 mb-1">Errori:</h3>
                        <ul class="text-red-600 text-sm space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li>• <?php echo esc_html($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Google OAuth Button -->
        <div class="mb-6">
            <button id="google-auth-btn" 
                    class="w-full bg-white border border-gray-200 text-gray-700 font-medium py-3 px-6 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center justify-center space-x-3">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5">
                <span><?php echo $mode === 'register' ? 'Registrati' : 'Accedi'; ?> con Google</span>
            </button>
            
            <button id="google-loading" 
                    class="w-full bg-gray-100 text-gray-500 font-medium py-3 px-6 rounded-xl cursor-not-allowed hidden flex items-center justify-center space-x-3">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <span>Elaborazione...</span>
            </button>
        </div>

        <!-- Separatore -->
        <div class="relative mb-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-gray-50 text-gray-500">oppure con email</span>
            </div>
        </div>

        <?php if ($mode === 'login'): ?>
            <!-- FORM LOGIN -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <form method="post">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="mr-2 rounded">
                            <span class="text-sm text-gray-600">Ricordami</span>
                        </label>
                    </div>

                    <button type="submit" name="login_submit"
                            class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 rounded-xl hover:from-purple-600 hover:to-pink-600 transition">
                        ACCEDI
                    </button>
                </form>
            </div>

        <?php else: ?>
            <!-- FORM REGISTRAZIONE -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <form method="post">
                    <!-- Nome e Cognome -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                            <input type="text" name="nome" required
                                   value="<?php echo isset($_POST['nome']) ? esc_attr($_POST['nome']) : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                            <input type="text" name="cognome" required
                                   value="<?php echo isset($_POST['cognome']) ? esc_attr($_POST['cognome']) : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" required
                               value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50">
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50"
                               placeholder="Minimo 6 caratteri">
                    </div>

                    <!-- Username Instagram -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username Instagram *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">@</span>
                            <input type="text" name="instagram_username" required
                                   value="<?php echo isset($_POST['instagram_username']) ? esc_attr($_POST['instagram_username']) : ''; ?>"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50">
                        </div>
                    </div>

                    <!-- Squadre del cuore -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Squadra del cuore * (1-3 squadre)</label>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <?php 
                            $squadre = array('milan' => 'Milan', 'inter' => 'Inter', 'napoli' => 'Napoli', 'roma' => 'Roma', 'lazio' => 'Lazio', 'juventus' => 'Juventus', 'altro' => 'Altro', 'nessuna' => 'Nessuna');
                            $selected = isset($_POST['squadre_cuore']) ? $_POST['squadre_cuore'] : array();
                            
                            foreach ($squadre as $value => $label): 
                            ?>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="squadre_cuore[]" value="<?php echo $value; ?>" 
                                           <?php echo in_array($value, $selected) ? 'checked' : ''; ?>
                                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    <span><?php echo $label; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Privacy -->
                    <div class="mb-6">
                        <label class="flex items-start space-x-3">
                            <input type="checkbox" required class="mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-600">
                                Accetto i <a href="/regolamento" class="text-blue-500 hover:text-blue-600 underline">termini e condizioni</a> e la privacy policy
                            </span>
                        </label>
                    </div>

                    <button type="submit" name="register_submit"
                            class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 rounded-xl hover:from-purple-600 hover:to-pink-600 transition">
                        REGISTRATI
                    </button>
                </form>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Modal per completare registrazione Google -->
<div id="google-register-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">Completa la registrazione</h3>
        <p class="text-gray-600 text-sm mb-6 text-center">Aggiungi alcune informazioni per completare il tuo profilo</p>
        
        <form id="google-register-form">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Username Instagram *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">@</span>
                    <input type="text" id="google_instagram_username" required
                           class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50">
                </div>
            </div>

            <!-- Squadre del cuore Google -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Squadra del cuore * (1-3 squadre)</label>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <?php foreach ($squadre as $value => $label): ?>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="google_squadre_cuore[]" value="<?php echo $value; ?>" 
                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span><?php echo $label; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" id="cancel-google-register"
                        class="flex-1 bg-gray-200 text-gray-700 font-medium py-3 rounded-xl hover:bg-gray-300 transition">
                    Annulla
                </button>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 rounded-xl hover:from-purple-600 hover:to-pink-600 transition">
                    Completa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bottom Navigation -->
<nav id="bottom-nav" class="fixed bottom-0 left-0 right-0 w-full bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around items-center py-3 px-4 max-w-full mx-auto">
        
        <!-- Home/Concorsi -->
        <a href="/" class="flex flex-col items-center">
            <i class="fa-solid fa-home text-gray-600 text-xl mb-1"></i>
            <span class="text-gray-600 text-xs">Home</span>
        </a>
        
        <!-- Classifica -->
        <a href="/classifica" class="flex flex-col items-center">
            <i class="fa-regular fa-chart-bar text-gray-600 text-xl mb-1"></i>
            <span class="text-gray-600 text-xs">Classifica</span>
        </a>
        
        <!-- Regolamento -->
        <a href="/regolamento" class="flex flex-col items-center">
            <i class="fa-regular fa-file-lines text-gray-600 text-xl mb-1"></i>
            <span class="text-gray-600 text-xs">Regolamento</span>
        </a>
        
        <!-- Profilo/Login -->
        <a href="<?php echo $mode === 'login' ? '/register' : '/login'; ?>" class="flex flex-col items-center">
            <i class="fa-regular fa-user text-blue-500 text-xl mb-1"></i>
            <span class="text-blue-500 text-xs"><?php echo $mode === 'login' ? 'Registrati' : 'Accedi'; ?></span>
        </a>
        
    </div>
</nav>

<!-- Spacer per bottom nav -->
<div class="pb-20"></div>

<script>
// Configurazione Google
const clientId = '<?php echo defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : ''; ?>';

// Inizializzazione Google
function initGoogleAuth() {
    if (typeof google === 'undefined' || !google.accounts) {
        setTimeout(initGoogleAuth, 500);
        return;
    }
    
    google.accounts.id.initialize({
        client_id: clientId,
        callback: handleGoogleResponse,
        auto_select: false
    });
    
    setupGoogleButton();
}

function setupGoogleButton() {
    const button = document.getElementById('google-auth-btn');
    const loading = document.getElementById('google-loading');
    
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        button.classList.add('hidden');
        loading.classList.remove('hidden');
        
        google.accounts.id.prompt((notification) => {
            if (notification.isNotDisplayed()) {
                button.classList.remove('hidden');
                loading.classList.add('hidden');
            }
        });
    });
}

function handleGoogleResponse(response) {
    sendGoogleToken(response.credential);
}

function sendGoogleToken(token) {
    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=google_oauth&google_token=' + token
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'login') {
                window.location.href = data.redirect;
            } else if (data.action === 'register') {
                showGoogleRegistrationModal(data.user_data);
            }
        } else {
            alert('Errore: ' + data.message);
        }
    })
    .finally(() => {
        document.getElementById('google-auth-btn').classList.remove('hidden');
        document.getElementById('google-loading').classList.add('hidden');
    });
}

function showGoogleRegistrationModal(userData) {
    window.tempGoogleData = userData;
    document.getElementById('google-register-modal').classList.remove('hidden');
}

// Modal management
document.getElementById('cancel-google-register').addEventListener('click', function() {
    document.getElementById('google-register-modal').classList.add('hidden');
});

document.getElementById('google-register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const squadre = Array.from(formData.getAll('google_squadre_cuore[]'));
    
    if (squadre.length === 0) {
        alert('Seleziona almeno una squadra');
        return;
    }
    
    if (squadre.length > 3) {
        alert('Massimo 3 squadre');
        return;
    }
    
    const postData = 'action=complete_google_register' +
                    '&google_data=' + encodeURIComponent(JSON.stringify(window.tempGoogleData)) +
                    '&instagram_username=' + encodeURIComponent(document.getElementById('google_instagram_username').value) +
                    '&squadre_cuore=' + encodeURIComponent(JSON.stringify(squadre));
    
    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: postData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Errore: ' + (data.errors ? data.errors.join(', ') : data.message));
        }
    });
});

// Inizializza tutto
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initGoogleAuth, 200);
});
</script>

</body>
</html>
