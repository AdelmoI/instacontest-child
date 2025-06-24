<?php
/**
 * Template Name: Registrazione
 * Pagina di registrazione con stile identico al login
 */

get_header(); ?>

<body class="bg-gray-50">

    <!-- Contenuto Registrazione -->
    <section class="px-4 py-6 bg-gray-50 min-h-screen">
        <div class="max-w-md mx-auto md:max-w-lg lg:max-w-xl">
            
            <!-- Header Sezione -->
            <div class="text-center mb-6 md:mb-8">
                <img src="https://www.instacontest.it/wp-content/uploads/2025/06/Progetto-senza-titolo-52.png" 
                     alt="InstaContest" 
                     class="w-18 h-18 md:w-24 md:h-24 object-contain mx-auto mb-4">
                <h1 class="text-black font-bold text-2xl md:text-3xl mb-2">Registrati su InstaContest</h1>
                <p class="text-gray-500 text-lg md:text-xl">Partecipa ai contest e accumula punti!</p>
            </div>

            <?php
            // Gestione registrazione
            $errors = array();
            $success = false;

            if (isset($_POST['instacontest_register'])) {
                if (!wp_verify_nonce($_POST['register_nonce'], 'instacontest_register')) {
                    $errors[] = 'Errore di sicurezza. Riprova.';
                } else {
                    $nome = sanitize_text_field($_POST['nome']);
                    $cognome = sanitize_text_field($_POST['cognome']);
                    $email = sanitize_email($_POST['email']);
                    $password = $_POST['password'];
                    $instagram_username = sanitize_text_field($_POST['instagram_username']);
                    
                    // Validazioni
                    if (empty($nome)) $errors[] = 'Il nome è obbligatorio';
                    if (empty($cognome)) $errors[] = 'Il cognome è obbligatorio';
                    if (empty($email) || !is_email($email)) $errors[] = 'Email non valida';
                    if (empty($password) || strlen($password) < 6) $errors[] = 'Password minimo 6 caratteri';
                    if (empty($instagram_username)) $errors[] = 'Username Instagram obbligatorio';
                    
                    // Pulisci username Instagram
                    $instagram_username = ltrim($instagram_username, '@');
                    
                    if (email_exists($email)) {
                        $errors[] = 'Email già registrata';
                    }
                    
                    // Crea utente se nessun errore
                    if (empty($errors)) {
                        $user_id = wp_create_user($email, $password, $email);
                        
                        if (!is_wp_error($user_id)) {
                            wp_update_user(array(
                                'ID' => $user_id,
                                'first_name' => $nome,
                                'last_name' => $cognome,
                                'display_name' => $nome . ' ' . $cognome
                            ));
                            
                            update_user_meta($user_id, 'instagram_username', $instagram_username);
                            update_user_meta($user_id, 'total_points', 0);
                            
                            // Login automatico
                            wp_set_current_user($user_id);
                            wp_set_auth_cookie($user_id);
                            
                            $success = true;
                            echo '<script>setTimeout(function(){ window.location.href = "' . home_url('/profilo') . '"; }, 1000);</script>';
                        } else {
                            $errors[] = 'Errore durante la registrazione. Riprova.';
                        }
                    }
                }
            }
            ?>

            <!-- Messaggi di successo -->
            <?php if ($success): ?>
                <div class="bg-white border border-green-200 rounded-2xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="text-black font-bold">Registrazione completata!</h3>
                            <p class="text-gray-500 text-sm">Ti stiamo reindirizzando al tuo profilo...</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Messaggi di errore -->
            <?php if (!empty($errors)): ?>
                <div class="bg-white border border-red-200 rounded-2xl p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fa-solid fa-exclamation-triangle text-red-500 mr-3 text-xl mt-0.5"></i>
                        <div>
                            <h3 class="text-black font-bold mb-1">Correggi questi errori:</h3>
                            <ul class="text-gray-600 text-sm space-y-1">
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
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <form method="post" class="space-y-6">
                        <?php wp_nonce_field('instacontest_register', 'register_nonce'); ?>
                        
                        <!-- Nome e Cognome -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="nome" class="block text-black font-medium text-sm mb-2">
                                    Nome <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nome" 
                                       name="nome" 
                                       value="<?php echo isset($_POST['nome']) ? esc_attr($_POST['nome']) : ''; ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50" 
                                       placeholder="Mario"
                                       required>
                            </div>
                            <div>
                                <label for="cognome" class="block text-black font-medium text-sm mb-2">
                                    Cognome <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="cognome" 
                                       name="cognome" 
                                       value="<?php echo isset($_POST['cognome']) ? esc_attr($_POST['cognome']) : ''; ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50" 
                                       placeholder="Rossi"
                                       required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-black font-medium text-sm mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50" 
                                   placeholder="mario.rossi@email.com"
                                   required>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-black font-medium text-sm mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 pr-12" 
                                       placeholder="Minimo 6 caratteri"
                                       required>
                                <button type="button" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                        onclick="togglePassword()">
                                    <i class="fa-solid fa-eye" id="password-toggle-icon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Almeno 6 caratteri</p>
                        </div>

                        <!-- Username Instagram -->
                        <div>
                            <label for="instagram_username" class="block text-black font-medium text-sm mb-2">
                                Username Instagram <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">@</span>
                                <input type="text" 
                                       id="instagram_username" 
                                       name="instagram_username" 
                                       value="<?php echo isset($_POST['instagram_username']) ? esc_attr($_POST['instagram_username']) : ''; ?>"
                                       class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50" 
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
                                   class="mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-4 h-4"
                                   required>
                            <label for="privacy" class="text-sm text-gray-600">
                                Accetto i <a href="/regolamento" class="text-blue-500 hover:text-blue-600 underline">termini e condizioni</a> e la privacy policy
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                name="instacontest_register"
                                class="w-full btn-participate font-bold py-3 rounded-xl text-sm">
                            REGISTRATI
                        </button>

                    </form>
                </div>

                <!-- Google OAuth Section -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-50 text-gray-500">oppure</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button class="w-full bg-white border border-gray-200 text-gray-600 font-medium py-3 px-6 rounded-xl hover:bg-gray-50 transition duration-200 shadow-sm opacity-50 cursor-not-allowed" disabled>
                            <i class="fab fa-google mr-2 text-red-500"></i>
                            Registrati con Google (Coming Soon)
                        </button>
                    </div>
                </div>

                <!-- Link al login -->
                <div class="text-center mt-6">
                    <p class="text-gray-600">
                        Hai già un account? 
                        <a href="/login" class="text-blue-500 hover:text-blue-600 font-medium">Accedi qui</a>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </section>

</body>

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

<?php get_footer(); ?>
