<?php
/**
 * Template STANDALONE per login - ZERO WordPress hooks
 */

// Carica solo WordPress core
require_once('../../../wp-load.php');

// Disabilita TUTTO
remove_all_actions('init');
remove_all_actions('wp_loaded');
remove_all_actions('wp_head');
remove_all_actions('wp_footer');

// Redirect se già loggato
if (is_user_logged_in()) {
    header('Location: /profilo');
    exit;
}

// Gestione login PURA
if (isset($_POST['login_submit'])) {
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    $user = wp_authenticate($email, $password);
    
    if (!is_wp_error($user)) {
        // Login PURO senza hooks
        wp_clear_auth_cookie();
        wp_set_auth_cookie($user->ID, $remember, is_ssl());
        wp_set_current_user($user->ID);
        
        // Redirect IMMEDIATO
        header('Location: /profilo');
        exit;
    } else {
        $error = 'Email o password non corretti';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InstaContest</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <img src="https://www.instacontest.it/wp-content/uploads/2025/06/Progetto-senza-titolo-52.png" 
                 alt="InstaContest" 
                 class="w-24 h-24 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Accedi a InstaContest</h1>
        </div>

        <!-- Errori -->
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-600 text-sm"><?php echo esc_html($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <form method="post">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-sm text-gray-600">Ricordami</span>
                    </label>
                </div>

                <button type="submit" name="login_submit"
                        class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 rounded-xl hover:from-purple-600 hover:to-pink-600 transition">
                    ACCEDI
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="/register" class="text-blue-500 hover:text-blue-600">Non hai un account? Registrati</a>
        </div>

    </div>
</div>

</body>
</html>
