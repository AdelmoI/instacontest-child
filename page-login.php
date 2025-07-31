<?php
/**
 * Template Name: Login
 * Redirect alla pagina auth unificata
 */

// Redirect al nuovo sistema auth
$auth_file = get_stylesheet_directory() . '/auth.php';

if (file_exists($auth_file)) {
    $_GET['mode'] = 'login';
    include $auth_file;
    exit;
}

// Fallback se il file non esiste
wp_redirect(home_url('/wp-content/themes/instacontest-child/auth.php'));
exit;
?>
