<?php
/**
 * Template Name: Login
 * Pagina di login con stile identico alla homepage
 */

get_header(); ?>

<body class="bg-gray-50">

    <!-- Header identico alla homepage -->
    <header id="header" class="fixed top-0 w-full bg-white border-b border-gray-200 z-50">
        <div class="flex items-center justify-between px-4 py-3 max-w-6xl mx-auto lg:px-8">
            <a href="<?php echo home_url(); ?>" class="flex items-center space-x-2">
                <img src="https://www.instacontest.it/wp-content/uploads/2025/06/Progetto-senza-titolo-52.png" 
                     alt="InstaContest" 
                     class="w-8 h-8 lg:w-10 lg:h-10">
                <span class="hidden sm:block text-black font-bold text-lg lg:text-xl">InstaContest</span>
            </a>
            <div></div>
            <a href="/register" class="text-black text-sm font-medium lg:text-base hover:text-blue-500 transition">
                Registrati
            </a>
        </div>
    </header>

    <!-- Contenuto Login -->
    <section class="mt-16 px-4 py-6 bg-gray-50 min-h-screen lg:py-12">
        <div class="max-w-md mx-auto lg:max-w-lg">
            
            <!-- Header Sezione -->
            <div class="text-center mb-6 lg:mb-8">
                <div class="w-16 h-16 lg:w-20 lg:h-20 instagram-gradient rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-sign-in-alt text-white text-xl lg:text-2xl"></i>
                </div>
                <h1 class="text-black font-bold text-2xl lg:text-3xl mb-2">Benvenuto su InstaContest</h1>
                <p class="text-gray-500 text-lg lg:text-xl">Accedi al tuo account</p>
            </div>

            <?php
            // Gestione login
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

            <!-- Form di login -->
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

                <!-- Google OAuth Section -->
                <div class="mt-6 lg:mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm lg:text-base">
                            <span class="px-2 bg-gray-50 text-gray-500">oppure</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button class="w-full bg-white border border-gray-200 text-gray-600 font-medium py-3 lg:py-4 px-6 rounded-xl hover:bg-gray-50 transition duration-200 shadow-sm opacity-50 cursor-not-allowed text-sm lg:text-base" disabled>
                            <i class="fab fa-google mr-2 text-red-500"></i>
                            Accedi con Google (Coming Soon)
                        </button>
                    </div>
                </div>

                <!-- Link alla registrazione -->
                <div class="text-center mt-6 lg:mt-8">
                    <p class="text-gray-600 text-sm lg:text-base">
                        Non hai ancora un account? 
                        <a href="/register" class="text-blue-500 hover:text-blue-600 font-medium transition">Registrati qui</a>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </section>

</body>

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

<?php get_footer(); ?>
