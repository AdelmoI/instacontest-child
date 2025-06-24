<?php
/**
 * Template Part: Form Registrazione
 * Semplice e user-friendly
 */

// Se utente già loggato, redirect
if (is_user_logged_in()) {
    wp_redirect(home_url('/profilo'));
    exit;
}

// Gestione errori
$errors = array();
$success = false;

// Elaborazione form
if (isset($_POST['instacontest_register'])) {
    
    // Nonce security check
    if (!wp_verify_nonce($_POST['register_nonce'], 'instacontest_register')) {
        $errors[] = 'Errore di sicurezza. Riprova.';
    } else {
        
        // Sanificazione dati
        $nome = sanitize_text_field($_POST['nome']);
        $cognome = sanitize_text_field($_POST['cognome']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $instagram_username = sanitize_text_field($_POST['instagram_username']);
        
        // Validazioni base
        if (empty($nome)) $errors[] = 'Il nome è obbligatorio';
        if (empty($cognome)) $errors[] = 'Il cognome è obbligatorio';
        if (empty($email) || !is_email($email)) $errors[] = 'Email non valida';
        if (empty($password) || strlen($password) < 6) $errors[] = 'Password minimo 6 caratteri';
        if (empty($instagram_username)) $errors[] = 'Username Instagram obbligatorio';
        
        // Pulisci username Instagram (rimuovi @ se presente)
        $instagram_username = ltrim($instagram_username, '@');
        
        // Controlla se email già esiste
        if (email_exists($email)) {
            $errors[] = 'Email già registrata';
        }
        
        // Se nessun errore, crea utente
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
                
                // Salva username Instagram
                update_user_meta($user_id, 'instagram_username', $instagram_username);
                
                // Inizializza punti a 0
                update_user_meta($user_id, 'total_points', 0);
                
                // Login automatico
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                
                $success = true;
                
                // Redirect dopo 2 secondi
                echo '<script>setTimeout(function(){ window.location.href = "' . home_url('/profilo') . '"; }, 2000);</script>';
                
            } else {
                $errors[] = 'Errore durante la registrazione. Riprova.';
            }
        }
    }
}
?>

<div class="register-form-container">
    <div class="container max-w-md mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mb-4">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Registrati su InstaContest</h1>
            <p class="text-gray-600">Partecipa ai contest e accumula punti!</p>
        </div>

        <!-- Messaggi di successo -->
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <div>
                        <h3 class="text-green-800 font-semibold">Registrazione completata!</h3>
                        <p class="text-green-700 text-sm">Ti stiamo reindirizzando al tuo profilo...</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Messaggi di errore -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-0.5"></i>
                    <div>
                        <h3 class="text-red-800 font-semibold mb-1">Correggi questi errori:</h3>
                        <ul class="text-red-700 text-sm space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li>• <?php echo esc_html($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form di registrazione -->
        <?php if (!$success): ?>
            <form method="post" class="space-y-6" id="register-form">
                <?php wp_nonce_field('instacontest_register', 'register_nonce'); ?>
                
                <!-- Nome e Cognome -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               value="<?php echo isset($_POST['nome']) ? esc_attr($_POST['nome']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                               placeholder="Mario"
                               required>
                    </div>
                    <div>
                        <label for="cognome" class="block text-sm font-medium text-gray-700 mb-2">
                            Cognome <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="cognome" 
                               name="cognome" 
                               value="<?php echo isset($_POST['cognome']) ? esc_attr($_POST['cognome']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                               placeholder="Rossi"
                               required>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                           placeholder="mario.rossi@email.com"
                           required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 pr-12" 
                               placeholder="Minimo 6 caratteri"
                               required>
                        <button type="button" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-toggle-icon"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Almeno 6 caratteri</p>
                </div>

                <!-- Username Instagram -->
                <div>
                    <label for="instagram_username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username Instagram <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">@</span>
                        <input type="text" 
                               id="instagram_username" 
                               name="instagram_username" 
                               value="<?php echo isset($_POST['instagram_username']) ? esc_attr($_POST['instagram_username']) : ''; ?>"
                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                               placeholder="mariorossi"
                               required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Necessario per partecipare ai contest</p>
                </div>

                <!-- Privacy -->
                <div class="flex items-start space-x-3">
                    <input type="checkbox" 
                           id="privacy" 
                           name="privacy" 
                           class="mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                           required>
                    <label for="privacy" class="text-sm text-gray-600">
                        Accetto i <a href="/regolamento" class="text-purple-600 hover:text-purple-700 underline">termini e condizioni</a> e la privacy policy
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        name="instacontest_register"
                        class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-600 hover:to-pink-600 transform hover:scale-105 transition duration-200 shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Registrati
                </button>

            </form>

            <!-- Link al login -->
            <div class="text-center mt-6">
                <p class="text-gray-600">
                    Hai già un account? 
                    <a href="/login" class="text-purple-600 hover:text-purple-700 font-semibold">Accedi qui</a>
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- JavaScript per toggle password -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('password-toggle-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
