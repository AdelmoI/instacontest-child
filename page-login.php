<?php
/**
 * Template Name: Login InstaContest
 * Template per login utenti personalizzato
 */

// Reindirizza se già loggato
if (is_user_logged_in()) {
    wp_redirect(home_url('/profilo/'));
    exit;
}

// Gestisci form submission
$login_errors = array();
$success_message = '';

if (isset($_POST['instacontest_login'])) {
    // Nonce security check
    if (!wp_verify_nonce($_POST['instacontest_login_nonce'], 'instacontest_login_action')) {
        $login_errors[] = 'Errore di sicurezza. Riprova.';
    } else {
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;
        
        if (empty($username)) {
            $login_errors[] = 'Username o email sono obbligatori.';
        }
        
        if (empty($password)) {
            $login_errors[] = 'Password è obbligatoria.';
        }
        
        if (empty($login_errors)) {
            $creds = array(
                'user_login'    => $username,
                'user_password' => $password,
                'remember'      => $remember
            );
            
            $user = wp_signon($creds, false);
            
            if (is_wp_error($user)) {
                $login_errors[] = 'Username/email o password non corretti.';
            } else {
                // Login successful - redirect
                wp_redirect(home_url('/profilo/?login=1'));
                exit;
            }
        }
    }
}

// Messaggi dal GET
$message = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'logout':
            $message = 'Logout effettuato con successo.';
            break;
        case 'registration':
            $message = 'Registrazione completata! Ora puoi accedere.';
            break;
        case 'instagram_required':
            $login_errors[] = 'Devi completare il tuo profilo aggiungendo l\'username Instagram.';
            break;
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
            
            <h2 class="text-2xl font-bold text-white mb-2">Bentornato!</h2>
            <p class="text-white/80">Accedi per continuare a vincere</p>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            
            <!-- Messaggio di successo -->
            <?php if (!empty($message)): ?>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <p class="text-green-800"><?php echo esc_html($message); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Errori -->
            <?php if (!empty($login_errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <h4 class="text-red-800 font-semibold">Errore di accesso:</h4>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        <?php foreach ($login_errors as $error): ?>
                            <li>• <?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="post" action="" class="space-y-6">
                <?php wp_nonce_field('instacontest_login_action', 'instacontest_login_nonce'); ?>
                
                <!-- Username/Email -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-gray-400 mr-1"></i>
                        Username o Email
                    </label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-all duration-200"
                           placeholder="Inserisci username o email"
                           required>
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-gray-400 mr-1"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-all duration-200"
                               placeholder="Inserisci la tua password"
                               required>
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="remember" value="1" 
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-600">Ricordami</span>
                    </label>
                    
                    <a href="<?php echo wp_lostpassword_url(); ?>" 
                       class="text-sm text-purple-600 hover:underline">
                        Password dimenticata?
                    </a>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="instacontest_login" 
                        class="w-full instagram-gradient text-white font-bold py-4 px-6 rounded-xl hover:instagram-gradient-hover transform transition-all duration-200 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Accedi
                </button>
            </form>
            
            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">oppure</span>
                </div>
            </div>
            
            <!-- Google Login Button (placeholder) -->
            <button type="button" 
                    class="w-full bg-white border-2 border-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-50 transition-all duration-200 flex items-center justify-center space-x-3">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Continua con Google</span>
            </button>
            
            <!-- Registration Link -->
            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-600">
                    Non hai ancora un account? 
                    <a href="<?php echo home_url('/registrazione/'); ?>" class="text-purple-600 font-semibold hover:underline">
                        Registrati ora
                    </a>
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-gift text-purple-500 mr-1"></i>
                    Registrandoti ottieni 10 punti gratuiti!
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

<!-- JavaScript -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordEye = document.getElementById('password-eye');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordEye.classList.remove('fa-eye');
        passwordEye.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordEye.classList.remove('fa-eye-slash');
        passwordEye.classList.add('fa-eye');
    }
}

// Auto-focus su username quando pagina carica
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('username').focus();
});
</script>

<style>
/* CSS specifico per il form login */
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
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
}

button:active {
    transform: scale(0.98);
}

/* Pulsing effect per il logo quando si carica */
@keyframes pulse-logo {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.logo-gradient {
    animation: pulse-logo 2s infinite;
}
</style>

<?php get_footer(); ?>
