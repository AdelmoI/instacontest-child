<?php
/**
 * Template Name: Login
 * Pagina di login con Google OAuth integrato
 */

get_header(); ?>

<body class="bg-gray-50">

    <!-- Contenuto Login -->
    <section class="px-4 py-6 bg-gray-50 min-h-screen">
        <div class="max-w-md mx-auto md:max-w-lg lg:max-w-xl">
            
            <!-- Header Sezione -->
            <div class="text-center mb-6 md:mb-8">
                <img src="https://www.instacontest.it/wp-content/uploads/2025/06/Progetto-senza-titolo-52.png" 
                     alt="InstaContest" 
                     class="w-18 h-18 md:w-24 md:h-24 object-contain mx-auto mb-4">
                <h1 class="text-black font-bold text-2xl md:text-3xl mb-2">Benvenuto su InstaContest</h1>
                <p class="text-gray-500 text-lg md:text-xl">Accedi al tuo account</p>
            </div>

            <?php
            // Gestione login normale (manteniamo il codice esistente)
            $errors = array();
            $success = false;

            if (isset($_POST['instacontest_login'])) {
                if (!wp_verify_nonce($_POST['login_nonce'], 'instacontest_login')) {
                    $errors[] = 'Errore di sicurezza. Riprova.';
                } else {
                    $email = sanitize_email($_POST['email']);
                    $password = $_POST['password'];
                    $remember = isset($_POST['remember']) ? true : false;
                    
                    if (empty($email)) $errors[] = 'Email obbligatoria';
                    if (empty($password)) $errors[] = 'Password obbligatoria';
                    
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
                            echo '<script>setTimeout(function(){ window.location.href = "' . home_url('/profilo') . '"; }, 1000);</script>';
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
                            <h3 class="text-black font-bold">Login effettuato!</h3>
                            <p class="text-gray-500 text-sm">Ti stiamo reindirizzando...</p>
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
                            <h3 class="text-black font-bold mb-1">Errore:</h3>
                            <ul class="text-gray-600 text-sm space-y-1">
                                <?php foreach ($errors as $error): ?>
                                    <li>• <?php echo esc_html($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- NUOVO: Google OAuth Section PRIMA del form normale -->
            <div class="mb-6">
                <button id="google-login-btn" 
                        class="w-full bg-white border border-gray-200 text-gray-700 font-medium py-3 px-6 rounded-xl hover:bg-gray-50 transition duration-200 shadow-sm flex items-center justify-center space-x-3">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5">
                    <span>Accedi con Google</span>
                </button>
                
                <!-- Loading state -->
                <button id="google-login-loading" 
                        class="w-full bg-gray-100 text-gray-500 font-medium py-3 px-6 rounded-xl cursor-not-allowed hidden flex items-center justify-center space-x-3">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <span>Accesso in corso...</span>
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

            <!-- Form di login normale (manteniamo il codice esistente) -->
            <?php if (!$success): ?>
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <form method="post" class="space-y-6">
                        <?php wp_nonce_field('instacontest_login', 'login_nonce'); ?>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-black font-medium text-sm mb-2">
                                Email
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50" 
                                   placeholder="mario.rossi@email.com"
                                   required>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-black font-medium text-sm mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 pr-12" 
                                       placeholder="La tua password"
                                       required>
                                <button type="button" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                        onclick="toggleLoginPassword()">
                                    <i class="fa-solid fa-eye" id="login-password-toggle-icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember me e Password dimenticata -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="remember" 
                                       name="remember" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                <label for="remember" class="ml-2 text-sm text-gray-600">
                                    Ricordami
                                </label>
                            </div>
                            <a href="<?php echo wp_lostpassword_url(); ?>" class="text-sm text-blue-500 hover:text-blue-600">
                                Password dimenticata?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                name="instacontest_login"
                                class="w-full btn-participate font-bold py-3 rounded-xl text-sm">
                            ACCEDI
                        </button>

                    </form>
                </div>

                <!-- Link alla registrazione -->
                <div class="text-center mt-6">
                    <p class="text-gray-600">
                        Non hai ancora un account? 
                        <a href="/register" class="text-blue-500 hover:text-blue-600 font-medium">Registrati qui</a>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- MODAL per completare registrazione Google -->
    <div id="google-register-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">Completa la registrazione</h3>
            <p class="text-gray-600 text-sm mb-6 text-center">Aggiungi alcune informazioni per completare il tuo profilo</p>
            
            <form id="google-register-form" class="space-y-4">
                <!-- Username Instagram -->
                <div>
                    <label for="google_instagram_username" class="block text-black font-medium text-sm mb-2">
                        Username Instagram <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">@</span>
                        <input type="text" 
                               id="google_instagram_username" 
                               name="instagram_username" 
                               class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50" 
                               placeholder="tusername"
                               required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Necessario per partecipare ai contest</p>
                </div>

                <!-- Privacy -->
                <div class="flex items-start space-x-3">
                    <input type="checkbox" 
                           id="google_accept_terms" 
                           name="accept_terms" 
                           class="mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-4 h-4"
                           required>
                    <label for="google_accept_terms" class="text-sm text-gray-600">
                        Accetto i <a href="/regolamento" class="text-blue-500 hover:text-blue-600 underline">termini e condizioni</a> e la privacy policy
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-3 mt-6">
                    <button type="button" 
                            id="cancel-google-register"
                            class="flex-1 bg-gray-200 text-gray-700 font-medium py-3 rounded-xl hover:bg-gray-300 transition">
                        Annulla
                    </button>
                    <button type="submit" 
                            class="flex-1 btn-participate font-bold py-3 rounded-xl text-sm">
                        Completa Registrazione
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- SCRIPT DI DEBUG - Aggiungilo temporaneamente prima di </body> -->
<script>
console.log('🔍 DEBUG Google OAuth - Inizio');

// Test 1: Verifica se le costanti PHP sono passate correttamente
console.log('Client ID:', '<?php echo defined("GOOGLE_CLIENT_ID") ? GOOGLE_CLIENT_ID : "NON DEFINITO"; ?>');

// Test 2: Verifica caricamento Google API
window.addEventListener('load', function() {
    setTimeout(function() {
        console.log('🔍 Gapi disponibile?', typeof gapi !== 'undefined');
        console.log('🔍 Gapi auth2?', typeof gapi !== 'undefined' && gapi.auth2);
        
        // Test 3: Verifica se il pulsante esiste
        const button = document.getElementById('google-login-btn');
        console.log('🔍 Pulsante trovato?', button !== null);
        
        if (button) {
            console.log('🔍 Event listeners sul pulsante:', getEventListeners ? getEventListeners(button) : 'Non disponibile in questo browser');
        }
        
        // Test 4: Forza click per vedere se l'handler è attaccato
        if (button) {
            button.addEventListener('click', function() {
                console.log('🔍 Click rilevato sul pulsante Google!');
            });
        }
        
    }, 2000);
});

// Test 5: Verifica errori Google API
window.gapi_onload = function() {
    console.log('🔍 Google API caricata tramite callback');
};

// Test 6: Override delle funzioni per debug
const originalOnGoogleApiLoad = window.onGoogleApiLoad;
window.onGoogleApiLoad = function() {
    console.log('🔍 onGoogleApiLoad chiamata');
    if (typeof originalOnGoogleApiLoad === 'function') {
        try {
            originalOnGoogleApiLoad();
            console.log('✅ onGoogleApiLoad eseguita con successo');
        } catch (error) {
            console.error('❌ Errore in onGoogleApiLoad:', error);
        }
    }
};

const originalInitGoogleAuth = window.initGoogleAuth;
window.initGoogleAuth = function() {
    console.log('🔍 initGoogleAuth chiamata');
    if (typeof originalInitGoogleAuth === 'function') {
        try {
            originalInitGoogleAuth();
            console.log('✅ initGoogleAuth eseguita con successo');
        } catch (error) {
            console.error('❌ Errore in initGoogleAuth:', error);
        }
    }
};

const originalAttachGoogleSignIn = window.attachGoogleSignIn;
window.attachGoogleSignIn = function() {
    console.log('🔍 attachGoogleSignIn chiamata');
    if (typeof originalAttachGoogleSignIn === 'function') {
        try {
            originalAttachGoogleSignIn();
            console.log('✅ attachGoogleSignIn eseguita con successo');
        } catch (error) {
            console.error('❌ Errore in attachGoogleSignIn:', error);
        }
    }
};

console.log('🔍 DEBUG Google OAuth - Fine setup');
</script>

</body>

<!-- JavaScript per Google OAuth e form handling -->
<script>
// Attendi che Google API sia caricata
function onGoogleApiLoad() {
    gapi.load('auth2', initGoogleAuth);
}

function initGoogleAuth() {
    gapi.auth2.init({
        client_id: '<?php echo GOOGLE_CLIENT_ID; ?>'
    }).then(function() {
        attachGoogleSignIn();
    });
}

function attachGoogleSignIn() {
    const authInstance = gapi.auth2.getAuthInstance();
    const button = document.getElementById('google-login-btn');
    const loading = document.getElementById('google-login-loading');
    
    button.addEventListener('click', function() {
        // Mostra loading
        button.classList.add('hidden');
        loading.classList.remove('hidden');
        
        authInstance.signIn().then(function(googleUser) {
            const idToken = googleUser.getAuthResponse().id_token;
            handleGoogleLogin(idToken);
        }).catch(function(error) {
            console.error('Errore Google Sign-In:', error);
            // Ripristina pulsante
            button.classList.remove('hidden');
            loading.classList.add('hidden');
        });
    });
}

function handleGoogleLogin(idToken) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=google_oauth_login&google_token=' + idToken + '&nonce=<?php echo wp_create_nonce('google_oauth_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data.action === 'login') {
                // Login esistente - redirect
                showSuccessMessage(data.data.message);
                setTimeout(() => {
                    window.location.href = data.data.redirect;
                }, 1000);
            } else if (data.data.action === 'register') {
                // Nuovo utente - mostra modal registrazione
                showGoogleRegisterModal(data.data.user_data);
            }
        } else {
            showErrorMessage(data.data);
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showErrorMessage('Errore durante l\'autenticazione');
    })
    .finally(() => {
        // Ripristina pulsante
        document.getElementById('google-login-btn').classList.remove('hidden');
        document.getElementById('google-login-loading').classList.add('hidden');
    });
}

function showGoogleRegisterModal(userData) {
    const modal = document.getElementById('google-register-modal');
    modal.classList.remove('hidden');
    
    // Salva dati utente temporaneamente
    window.tempGoogleData = userData;
    
    // Gestione form completamento registrazione
    const form = document.getElementById('google-register-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        completeGoogleRegistration();
    });
    
    // Gestione annullamento
    document.getElementById('cancel-google-register').addEventListener('click', function() {
        modal.classList.add('hidden');
    });
}

function completeGoogleRegistration() {
    const formData = new FormData(document.getElementById('google-register-form'));
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=complete_google_registration' +
              '&google_data=' + encodeURIComponent(JSON.stringify(window.tempGoogleData)) +
              '&instagram_username=' + formData.get('instagram_username') +
              '&accept_terms=' + (formData.get('accept_terms') ? '1' : '0') +
              '&nonce=<?php echo wp_create_nonce('google_oauth_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.data.message);
            setTimeout(() => {
                window.location.href = data.data.redirect;
            }, 1000);
        } else {
            showErrorMessage(data.data);
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showErrorMessage('Errore durante la registrazione');
    });
}

function showSuccessMessage(message) {
    // Rimuovi messaggi esistenti
    const existing = document.querySelectorAll('.temp-message');
    existing.forEach(el => el.remove());
    
    const successDiv = document.createElement('div');
    successDiv.className = 'temp-message bg-white border border-green-200 rounded-2xl p-4 mb-6';
    successDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fa-solid fa-check-circle text-green-500 mr-3 text-xl"></i>
            <div>
                <h3 class="text-black font-bold">Successo!</h3>
                <p class="text-gray-500 text-sm">${message}</p>
            </div>
        </div>
    `;
    
    const container = document.querySelector('.max-w-md');
    container.insertBefore(successDiv, container.children[1]);
}

function showErrorMessage(message) {
    // Rimuovi messaggi esistenti
    const existing = document.querySelectorAll('.temp-message');
    existing.forEach(el => el.remove());
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'temp-message bg-white border border-red-200 rounded-2xl p-4 mb-6';
    
    let errorText = '';
    if (Array.isArray(message)) {
        errorText = message.map(err => `• ${err}`).join('<br>');
    } else {
        errorText = message;
    }
    
    errorDiv.innerHTML = `
        <div class="flex items-start">
            <i class="fa-solid fa-exclamation-triangle text-red-500 mr-3 text-xl mt-0.5"></i>
            <div>
                <h3 class="text-black font-bold mb-1">Errore:</h3>
                <div class="text-gray-600 text-sm">${errorText}</div>
            </div>
        </div>
    `;
    
    const container = document.querySelector('.max-w-md');
    container.insertBefore(errorDiv, container.children[1]);
}

// Toggle password esistente
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

// Carica Google API
if (typeof gapi === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://apis.google.com/js/platform.js?onload=onGoogleApiLoad';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
} else {
    onGoogleApiLoad();
}
</script>

<?php get_footer(); ?>
