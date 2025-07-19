<?php
/**
 * Template Name: Registrazione
 * Pagina di registrazione con Google OAuth integrato
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
            // Gestione registrazione normale
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
                    $squadre_cuore = isset($_POST['squadre_cuore']) ? $_POST['squadre_cuore'] : array();
                    
                    // Validazioni
                    if (empty($nome)) $errors[] = 'Il nome è obbligatorio';
                    if (empty($cognome)) $errors[] = 'Il cognome è obbligatorio';
                    if (empty($email) || !is_email($email)) $errors[] = 'Email non valida';
                    if (empty($password) || strlen($password) < 6) $errors[] = 'Password minimo 6 caratteri';
                    if (empty($instagram_username)) $errors[] = 'Username Instagram obbligatorio';
                    
                    // Validazione squadre del cuore
                    if (empty($squadre_cuore)) {
                        $errors[] = 'Seleziona almeno una squadra del cuore';
                    } elseif (count($squadre_cuore) > 3) {
                        $errors[] = 'Puoi selezionare massimo 3 squadre del cuore';
                    } else {
                        // Valida che le squadre siano nell'elenco consentito
                        $squadre_consentite = array('milan', 'inter', 'napoli', 'roma', 'lazio', 'juventus', 'altro', 'nessuna');
                        foreach ($squadre_cuore as $squadra) {
                            if (!in_array($squadra, $squadre_consentite)) {
                                $errors[] = 'Squadra non valida selezionata';
                                break;
                            }
                        }
                    }
                    
                    $instagram_username = ltrim($instagram_username, '@');
                    
                    if (email_exists($email)) {
                        $errors[] = 'Email già registrata';
                    }
                    
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
                            update_user_meta($user_id, 'squadre_cuore', $squadre_cuore);
                            update_user_meta($user_id, 'total_points', 0);
                            
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

            <!-- Google OAuth Section -->
            <div class="mb-6">
                <button id="google-register-btn" 
                        class="w-full bg-white border border-gray-200 text-gray-700 font-medium py-3 px-6 rounded-xl hover:bg-gray-50 transition duration-200 shadow-sm flex items-center justify-center space-x-3">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5">
                    <span>Registrati con Google</span>
                </button>
                
                <button id="google-register-loading" 
                        class="w-full bg-gray-100 text-gray-500 font-medium py-3 px-6 rounded-xl cursor-not-allowed hidden flex items-center justify-center space-x-3">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <span>Registrazione in corso...</span>
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

            <!-- Form di registrazione normale -->
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
                                        onclick="toggleRegisterPassword()">
                                    <i class="fa-solid fa-eye" id="register-password-toggle-icon"></i>
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

                        <!-- Squadre del cuore -->
                        <div>
                            <label class="block text-black font-medium text-sm mb-3">
                                Squadre del cuore <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Seleziona da 1 a 3 squadre</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_milan" 
                                           name="squadre_cuore[]" 
                                           value="milan"
                                           class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('milan', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_milan" class="text-sm text-gray-700">🔴 Milan</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_inter" 
                                           name="squadre_cuore[]" 
                                           value="inter"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('inter', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_inter" class="text-sm text-gray-700">🔵 Inter</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_napoli" 
                                           name="squadre_cuore[]" 
                                           value="napoli"
                                           class="rounded border-gray-300 text-blue-400 focus:ring-blue-400 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('napoli', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_napoli" class="text-sm text-gray-700">💙 Napoli</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_roma" 
                                           name="squadre_cuore[]" 
                                           value="roma"
                                           class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('roma', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_roma" class="text-sm text-gray-700">🟡 Roma</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_lazio" 
                                           name="squadre_cuore[]" 
                                           value="lazio"
                                           class="rounded border-gray-300 text-sky-500 focus:ring-sky-400 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('lazio', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_lazio" class="text-sm text-gray-700">🩵 Lazio</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_juventus" 
                                           name="squadre_cuore[]" 
                                           value="juventus"
                                           class="rounded border-gray-300 text-gray-900 focus:ring-gray-600 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('juventus', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_juventus" class="text-sm text-gray-700">⚫ Juventus</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_altro" 
                                           name="squadre_cuore[]" 
                                           value="altro"
                                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('altro', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_altro" class="text-sm text-gray-700">🟣 Altro</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="team_nessuna" 
                                           name="squadre_cuore[]" 
                                           value="nessuna"
                                           class="rounded border-gray-300 text-gray-400 focus:ring-gray-300 w-4 h-4"
                                           <?php echo (isset($_POST['squadre_cuore']) && in_array('nessuna', $_POST['squadre_cuore'])) ? 'checked' : ''; ?>>
                                    <label for="team_nessuna" class="text-sm text-gray-700">⭕ Non ho una squadra</label>
                                </div>
                            </div>
                            <div id="teams-error" class="text-red-500 text-xs mt-2 hidden">Seleziona almeno 1 squadra (massimo 3)</div>
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

    <!-- Modal per completare registrazione Google -->
    <div id="google-register-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">Completa la registrazione</h3>
            <p class="text-gray-600 text-sm mb-6 text-center">Aggiungi alcune informazioni per completare il tuo profilo</p>
            
            <form id="google-register-form" class="space-y-4">
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

                <!-- Squadre del cuore nel modal -->
                <div>
                    <label class="block text-black font-medium text-sm mb-3">
                        Squadre del cuore <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Seleziona da 1 a 3 squadre</p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_milan" name="squadre_cuore[]" value="milan" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4">
                            <label for="google_team_milan" class="text-xs text-gray-700">🔴 Milan</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_inter" name="squadre_cuore[]" value="inter" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <label for="google_team_inter" class="text-xs text-gray-700">🔵 Inter</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_napoli" name="squadre_cuore[]" value="napoli" class="rounded border-gray-300 text-blue-400 focus:ring-blue-400 w-4 h-4">
                            <label for="google_team_napoli" class="text-xs text-gray-700">💙 Napoli</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_roma" name="squadre_cuore[]" value="roma" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-4 h-4">
                            <label for="google_team_roma" class="text-xs text-gray-700">🟡 Roma</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_lazio" name="squadre_cuore[]" value="lazio" class="rounded border-gray-300 text-sky-500 focus:ring-sky-400 w-4 h-4">
                            <label for="google_team_lazio" class="text-xs text-gray-700">🩵 Lazio</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_juventus" name="squadre_cuore[]" value="juventus" class="rounded border-gray-300 text-gray-900 focus:ring-gray-600 w-4 h-4">
                            <label for="google_team_juventus" class="text-xs text-gray-700">⚫ Juventus</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_altro" name="squadre_cuore[]" value="altro" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-4 h-4">
                            <label for="google_team_altro" class="text-xs text-gray-700">🟣 Altro</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="google_team_nessuna" name="squadre_cuore[]" value="nessuna" class="rounded border-gray-300 text-gray-400 focus:ring-gray-300 w-4 h-4">
                            <label for="google_team_nessuna" class="text-xs text-gray-700">⭕ Nessuna</label>
                        </div>
                    </div>
                    <div id="google-teams-error" class="text-red-500 text-xs mt-2 hidden">Seleziona almeno 1 squadra (massimo 3)</div>
                </div>

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

</body>

<!-- Google Identity Services -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
const clientId = '<?php echo GOOGLE_CLIENT_ID; ?>';

function initGoogleIdentity() {
    if (typeof google === 'undefined' || !google.accounts) {
        setTimeout(initGoogleIdentity, 500);
        return;
    }
    
    google.accounts.id.initialize({
        client_id: clientId,
        callback: handleCredentialResponse,
        auto_select: false,
        cancel_on_tap_outside: true
    });
    
    setupCustomGoogleButton();
}

function handleCredentialResponse(response) {
    sendCredentialToServer(response.credential);
}

function setupCustomGoogleButton() {
    const button = document.getElementById('google-register-btn');
    const loading = document.getElementById('google-register-loading');
    
    if (!button) return;
    
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        button.classList.add('hidden');
        loading.classList.remove('hidden');
        
        try {
            google.accounts.id.prompt((notification) => {
                if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                    initiateOAuthFlow();
                }
                if (notification.isNotDisplayed()) {
                    button.classList.remove('hidden');
                    loading.classList.add('hidden');
                }
            });
        } catch (error) {
            button.classList.remove('hidden');
            loading.classList.add('hidden');
            initiateOAuthFlow();
        }
    });
}

function initiateOAuthFlow() {
    const client = google.accounts.oauth2.initTokenClient({
        client_id: clientId,
        scope: 'openid email profile',
        callback: (response) => {
            if (response.access_token) {
                fetchUserProfile(response.access_token);
            }
        },
    });
    client.requestAccessToken();
}

async function fetchUserProfile(accessToken) {
    try {
        const response = await fetch('https://www.googleapis.com/oauth2/v2/userinfo', {
            headers: { 'Authorization': `Bearer ${accessToken}` }
        });
        
        const userInfo = await response.json();
        
        const fakeCredential = btoa(JSON.stringify({
            sub: userInfo.id,
            email: userInfo.email,
            name: userInfo.name,
            given_name: userInfo.given_name,
            family_name: userInfo.family_name,
            picture: userInfo.picture,
            aud: clientId
        }));
        
        sendCredentialToServer(fakeCredential);
        
    } catch (error) {
        showMessage('error', 'Errore durante il recupero del profilo Google');
        document.getElementById('google-register-btn').classList.remove('hidden');
        document.getElementById('google-register-loading').classList.add('hidden');
    }
}

function sendCredentialToServer(credential) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=google_oauth_login&google_token=' + credential + '&nonce=<?php echo wp_create_nonce('google_oauth_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data.action === 'login') {
                showMessage('success', 'Accesso effettuato! Hai già un account.');
                setTimeout(() => window.location.href = data.data.redirect, 1000);
            } else if (data.data.action === 'register') {
                showRegistrationModal(data.data.user_data);
            }
        } else {
            showMessage('error', data.data || 'Errore sconosciuto');
        }
    })
    .catch(error => {
        showMessage('error', 'Errore di comunicazione con il server');
    })
    .finally(() => {
        document.getElementById('google-register-btn').classList.remove('hidden');
        document.getElementById('google-register-loading').classList.add('hidden');
    });
}

function showMessage(type, message) {
    const alertClass = type === 'success' ? 'border-green-200 text-green-800' : 'border-red-200 text-red-800';
    const icon = type === 'success' ? 'fa-check-circle text-green-500' : 'fa-exclamation-triangle text-red-500';
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `bg-white border ${alertClass} rounded-2xl p-4 mb-6`;
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fa-solid ${icon} mr-3 text-xl"></i>
            <div>
                <h3 class="font-bold">${type === 'success' ? 'Successo!' : 'Errore'}</h3>
                <p class="text-sm">${message}</p>
            </div>
        </div>
    `;
    
    const container = document.querySelector('.max-w-md');
    container.insertBefore(messageDiv, container.children[1]);
    
    setTimeout(() => {
        if (messageDiv.parentNode) messageDiv.parentNode.removeChild(messageDiv);
    }, 5000);
}

function showRegistrationModal(userData) {
    const modal = document.getElementById('google-register-modal');
    modal.classList.remove('hidden');
    
    window.tempGoogleData = userData;
    
    const form = document.getElementById('google-register-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        completeGoogleRegistration();
    });
    
    document.getElementById('cancel-google-register').addEventListener('click', function() {
        modal.classList.add('hidden');
    });
}

function completeGoogleRegistration() {
    const formData = new FormData(document.getElementById('google-register-form'));
    
    // Raccogli le squadre selezionate
    const squadreSelezionate = [];
    const checkboxes = document.querySelectorAll('#google-register-form input[name="squadre_cuore[]"]:checked');
    checkboxes.forEach(checkbox => squadreSelezionate.push(checkbox.value));
    
    // Validazione squadre
    if (squadreSelezionate.length === 0 || squadreSelezionate.length > 3) {
        document.getElementById('google-teams-error').classList.remove('hidden');
        return;
    } else {
        document.getElementById('google-teams-error').classList.add('hidden');
    }
    
    // Crea stringa per le squadre
    const squadreParam = squadreSelezionate.map(squadra => `squadre_cuore[]=${encodeURIComponent(squadra)}`).join('&');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=complete_google_registration' +
              '&google_data=' + encodeURIComponent(JSON.stringify(window.tempGoogleData)) +
              '&instagram_username=' + formData.get('instagram_username') +
              '&' + squadreParam +
              '&accept_terms=' + (formData.get('accept_terms') ? '1' : '0') +
              '&nonce=<?php echo wp_create_nonce('google_oauth_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('success', data.data.message);
            setTimeout(() => window.location.href = data.data.redirect, 1000);
        } else {
            showMessage('error', data.data);
        }
    })
    .catch(error => {
        showMessage('error', 'Errore durante la registrazione');
    });
}

function toggleRegisterPassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('register-password-toggle-icon');
    
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

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initGoogleIdentity, 200);
    
    // Validazione squadre del cuore in tempo reale
    const teamCheckboxes = document.querySelectorAll('input[name="squadre_cuore[]"]');
    const teamsError = document.getElementById('teams-error');
    
    teamCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedTeams = document.querySelectorAll('input[name="squadre_cuore[]"]:checked');
            
            if (checkedTeams.length === 0) {
                teamsError.textContent = 'Seleziona almeno 1 squadra';
                teamsError.classList.remove('hidden');
            } else if (checkedTeams.length > 3) {
                teamsError.textContent = 'Massimo 3 squadre selezionabili';
                teamsError.classList.remove('hidden');
                
                // Deseleziona l'ultimo checkbox selezionato
                this.checked = false;
            } else {
                teamsError.classList.add('hidden');
            }
        });
    });
    
    // Validazione squadre del cuore nel modal Google
    const googleTeamCheckboxes = document.querySelectorAll('#google-register-form input[name="squadre_cuore[]"]');
    const googleTeamsError = document.getElementById('google-teams-error');
    
    googleTeamCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedTeams = document.querySelectorAll('#google-register-form input[name="squadre_cuore[]"]:checked');
            
            if (checkedTeams.length === 0) {
                googleTeamsError.textContent = 'Seleziona almeno 1 squadra';
                googleTeamsError.classList.remove('hidden');
            } else if (checkedTeams.length > 3) {
                googleTeamsError.textContent = 'Massimo 3 squadre selezionabili';
                googleTeamsError.classList.remove('hidden');
                
                // Deseleziona l'ultimo checkbox selezionato
                this.checked = false;
            } else {
                googleTeamsError.classList.add('hidden');
            }
        });
    });
});

if (document.readyState !== 'loading') {
    setTimeout(initGoogleIdentity, 200);
}
</script>

<?php get_footer(); ?>
