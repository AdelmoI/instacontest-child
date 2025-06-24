<?php
/**
 * Template Part: Form Login
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
if (isset($_POST['instacontest_login'])) {
    
    // Nonce security check
    if (!wp_verify_nonce($_POST['login_nonce'], 'instacontest_login')) {
        $errors[] = 'Errore di sicurezza. Riprova.';
    } else {
        
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;
        
        // Validazioni base
        if (empty($email)) $errors[] = 'Email obbligatoria';
        if (empty($password)) $errors[] = 'Password obbligatoria';
        
        // Se nessun errore, prova login
        if (empty($errors)) {
            $creds = array(
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => $remember,
            );
            
            $user = wp_signon($creds, false);
            
            if (is_wp_error($user)) {
                $errors[] = 'Email o password non corretti';
            } else {
                $success = true;
                // Redirect dopo 1 secondo
                echo '<script>setTimeout(function(){ window.location.href = "' . home_url('/profilo') . '"; }, 1000);</script>';
            }
        }
    }
}
?>

<div class="login-form-container">
    <div class="container max-w-md mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full mb-4">
                <i class="fas fa-sign-in-alt text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Benvenuto su InstaContest</h1>
            <p class="text-gray-600">Accedi al tuo account</p>
        </div>

        <!-- Messaggi di successo -->
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <div>
                        <h3 class="text-green-800 font-semibold">Login effettuato!</h3>
                        <p class="text-green-700 text-sm">Ti stiamo reindirizzando...</p>
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
                        <h3 class="text-red-800 font-semibold mb-1">Errore:</h3>
                        <ul class="text-red-700 text-sm space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li>• <?php echo esc_html($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form di login -->
        <?php if (!$success): ?>
            <form method="post" class="space-y-6" id="login-form">
                <?php wp_nonce_field('instacontest_login', 'login_nonce'); ?>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                           placeholder="mario.rossi@email.com"
                           required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 pr-12" 
                               placeholder="La tua password"
                               required>
                        <button type="button" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                onclick="toggleLoginPassword()">
                            <i class="fas fa-eye" id="login-password-toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember me e Password dimenticata -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600">
                            Ricordami
                        </label>
                    </div>
                    <a href="<?php echo wp_lostpassword_url(); ?>" class="text-sm text-blue-600 hover:text-blue-700">
                        Password dimenticata?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        name="instacontest_login"
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition duration-200 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Accedi
                </button>

            </form>

            <!-- Link alla registrazione -->
            <div class="text-center mt-6">
                <p class="text-gray-600">
                    Non hai ancora un account? 
                    <a href="/register" class="text-blue-600 hover:text-blue-700 font-semibold">Registrati qui</a>
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- JavaScript per toggle password -->
<script>
function toggleLoginPassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('login-password-toggle-icon');
    
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
