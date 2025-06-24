<?php
/**
 * Template Name: Registrazione InstaContest
 * Template per registrazione utenti con campi custom
 */

// Reindirizza se già loggato
if (is_user_logged_in()) {
    wp_redirect(home_url('/profilo/'));
    exit;
}

// Gestisci form submission
$registration_errors = array();
$success_message = '';

if (isset($_POST['instacontest_register'])) {
    // Nonce security check
    if (!wp_verify_nonce($_POST['instacontest_register_nonce'], 'instacontest_register_action')) {
        $registration_errors[] = 'Errore di sicurezza. Riprova.';
    } else {
        // Raccogli e sanitizza dati
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $instagram_username = sanitize_text_field($_POST['instagram_username']);
        $phone = sanitize_text_field($_POST['phone']);
        $city = sanitize_text_field($_POST['city']);
        $birth_date = sanitize_text_field($_POST['birth_date']);
        
        // Validazioni
        if (empty($username)) {
            $registration_errors[] = 'Username è obbligatorio.';
        } elseif (strlen($username) < 3) {
            $registration_errors[] = 'Username deve essere di almeno 3 caratteri.';
        } elseif (username_exists($username)) {
            $registration_errors[] = 'Questo username è già in uso.';
        }
        
        if (empty($email)) {
            $registration_errors[] = 'Email è obbligatoria.';
        } elseif (!is_email($email)) {
            $registration_errors[] = 'Formato email non valido.';
        } elseif (email_exists($email)) {
            $registration_errors[] = 'Questa email è già registrata.';
        }
        
        if (empty($password)) {
            $registration_errors[] = 'Password è obbligatoria.';
        } elseif (strlen($password) < 6) {
            $registration_errors[] = 'Password deve essere di almeno 6 caratteri.';
        } elseif ($password !== $password_confirm) {
            $registration_errors[] = 'Le password non coincidono.';
        }
        
        if (empty($first_name)) {
            $registration_errors[] = 'Nome è obbligatorio.';
        }
        
        if (empty($last_name)) {
            $registration_errors[] = 'Cognome è obbligatorio.';
        }
        
        if (empty($instagram_username)) {
            $registration_errors[] = 'Username Instagram è obbligatorio.';
        } else {
            $instagram_clean = ltrim($instagram_username, '@');
            if (instacontest_get_user_by_instagram($instagram_clean)) {
                $registration_errors[] = 'Questo username Instagram è già registrato.';
            }
        }
        
        if (!empty($birth_date)) {
            $birth_timestamp = strtotime($birth_date);
            $min_age_timestamp = strtotime('-18 years');
            if ($birth_timestamp > $min_age_timestamp) {
                $registration_errors[] = 'Devi essere maggiorenne per registrarti.';
            }
        }
        
        // Privacy policy check
        if (!isset($_POST['privacy_accepted'])) {
            $registration_errors[] = 'Devi accettare la privacy policy.';
        }
        
        // Se non ci sono errori, crea utente
        if (empty($registration_errors)) {
            $user_data = array(
                'user_login' => $username,
                'user_email' => $email,
                'user_pass' => $password,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $first_name . ' ' . $last_name,
                'role' => 'subscriber'
            );
            
            $user_id = wp_insert_user($user_data);
            
            if (!is_wp_error($user_id)) {
                // Salva campi custom
                update_user_meta($user_id, 'instagram_username', ltrim($instagram_username, '@'));
                update_user_meta($user_id, 'user_phone', $phone);
                update_user_meta($user_id, 'user_city', $city);
                update_user_meta($user_id, 'birth_date', $birth_date);
                
                // Punti benvenuto
                instacontest_add_points_to_user($user_id, 10);
                
                // Auto login
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                
                // Redirect al profilo
                wp_redirect(home_url('/profilo/?welcome=1'));
                exit;
            } else {
                $registration_errors[] = 'Errore durante la registrazione: ' . $user_id->get_error_message();
            }
        }
    }
}

get_header(); ?>

<div class="min-h-screen bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 py-8">
    <div class="container mx-auto px-4 max-w-md">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="<?php echo home_url(); ?>" class="inline-block mb-6">
                <h1 class="text-4xl font-bold text-white">
                    <span class="text-5xl">🎯</span>
                    <span class="logo-gradient">InstaContest</span>
                </h1>
            </a>
            
            <h2 class="text-2xl font-bold text-white mb-2">Crea il tuo account</h2>
            <p class="text-white/80">Unisciti alla community e inizia a vincere!</p>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            
            <!-- Errori -->
            <?php if (!empty($registration_errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <h4 class="text-red-800 font-semibold">Errori di registrazione:</h4>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        <?php foreach ($registration_errors as $error): ?>
                            <li>• <?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Form -->
            <form method="post" action="" class="space-y-6">
                <?php wp_nonce_field('instacontest_register_action', 'instacontest_register_nonce'); ?>
                
                <!-- Nome e Cognome -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nome *
                        </label>
                        <input type="text" id="first_name" name="first_name" 
                               value="<?php echo isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : ''; ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors"
                               required>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Cognome *
                        </label>
                        <input type="text" id="last_name" name="last_name" 
                               value="<?php echo isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : ''; ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors"
                               required>
                    </div>
                </div>
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                        Username *
                    </label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors"
                           placeholder="Es: mario.rossi"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Almeno 3 caratteri, solo lettere, numeri e underscore</p>
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email *
                    </label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors"
                           required>
                </div>
                
                <!-- Instagram Username -->
                <div>
                    <label for="instagram_username" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fab fa-instagram text-pink-500 mr-1"></i>
                        Username Instagram *
                    </label>
                    <input type="text" id="instagram_username" name="instagram_username" 
                           value="<?php echo isset($_POST['instagram_username']) ? esc_attr($_POST['instagram_username']) : ''; ?>"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors"
                           placeholder="@iltuousername"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Necessario per partecipare ai contest</p>
                </div>
                
                <!-- Password -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password *
                        </label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors"
                               required>
                    </div>
                    <div>
                        <label for="password_confirm" class="block text-sm font-semibold text-gray-700 mb-2">
                            Conferma Password *
                        </label>
                        <input type="password" id="password_confirm" name="password_confirm" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors"
                               required>
                    </div>
                </div>
                
                <!-- Dati opzionali -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700 border-b pb-2">Informazioni aggiuntive (opzionali)</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-600 mb-2">
                                Telefono
                            </label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo isset($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-600 mb-2">
                                Città
                            </label>
                            <input type="text" id="city" name="city" 
                                   value="<?php echo isset($_POST['city']) ? esc_attr($_POST['city']) : ''; ?>"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors">
                        </div>
                    </div>
                    
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-600 mb-2">
                            Data di nascita
                        </label>
                        <input type="date" id="birth_date" name="birth_date" 
                               value="<?php echo isset($_POST['birth_date']) ? esc_attr($_POST['birth_date']) : ''; ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Devi essere maggiorenne</p>
                    </div>
                </div>
                
                <!-- Privacy Policy -->
                <div>
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" name="privacy_accepted" value="1" 
                               class="mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                               required>
                        <span class="text-sm text-gray-600">
                            Accetto la <a href="/privacy-policy/" class="text-purple-600 hover:underline">Privacy Policy</a> 
                            e i <a href="/regolamento/" class="text-purple-600 hover:underline">Termini di Servizio</a> *
                        </span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="instacontest_register" 
                        class="w-full instagram-gradient text-white font-bold py-4 px-6 rounded-xl hover:instagram-gradient-hover transform transition-all duration-200 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Crea Account (+10 punti benvenuto!)
                </button>
            </form>
            
            <!-- Login Link -->
            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-600">
                    Hai già un account? 
                    <a href="<?php echo wp_login_url(); ?>" class="text-purple-600 font-semibold hover:underline">
                        Accedi qui
                    </a>
                </p>
            </div>
            
        </div>
        
        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="<?php echo home_url(); ?>" class="text-white/80 hover:text-white transition-colors">
                ← Torna alla homepage
            </a>
        </div>
        
    </div>
</div>

<style>
/* CSS specifico per il form registrazione */
.instagram-gradient {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}

.instagram-gradient-hover {
    background: linear-gradient(45deg, #e6893c 0%, #d55a35 25%, #c5213c 50%, #b51c5f 75%, #a51681 100%);
}

.logo-gradient {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Form animations */
input:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

button:active {
    transform: scale(0.98);
}
</style>

<?php get_footer(); ?>
