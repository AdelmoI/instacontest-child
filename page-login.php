<?php
/**
 * Template Name: Login
 * Include il sistema auth unificato
 */

// Non chiamare get_header() per evitare conflitti
if (!is_user_logged_in()) {
    // Imposta modalità login
    $_GET['mode'] = 'login';
    
    // Include direttamente il file auth
    $auth_file = get_stylesheet_directory() . '/auth.php';
    
    if (file_exists($auth_file)) {
        include $auth_file;
        exit;
    }
} else {
    // Se già loggato, redirect al profilo
    wp_redirect(home_url('/profilo'));
    exit;
}
?>
