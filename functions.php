<?php
/**
 * INSTACONTEST CHILD THEME - FUNCTIONS.PHP FINALE
 * Versione pulita e funzionante
 */

// Previeni accesso diretto
if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// 1. ENQUEUE STYLES E SCRIPTS
// ========================================

function instacontest_enqueue_styles() {
    // FontAwesome
    wp_enqueue_style('fontawesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', 
        array(), 
        '6.4.0'
    );
    
    // Tema parent Astra
    wp_enqueue_style('astra-parent-style', 
        get_template_directory_uri() . '/style.css'
    );
    
    // Child theme CSS
    wp_enqueue_style('instacontest-style', 
        get_stylesheet_directory_uri() . '/style.css',
        array('astra-parent-style', 'fontawesome'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'instacontest_enqueue_styles');

function instacontest_enqueue_scripts() {
    // JavaScript personalizzato
    wp_enqueue_script('instacontest-js', 
        get_stylesheet_directory_uri() . '/instacontest.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // AJAX localization
    wp_localize_script('instacontest-js', 'instacontest_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('track_participation')
    ));
}
add_action('wp_enqueue_scripts', 'instacontest_enqueue_scripts');

// ========================================
// 2. CUSTOM POST TYPE CONTEST
// ========================================

function instacontest_register_contest_cpt() {
    $labels = array(
        'name'               => 'Contest',
        'singular_name'      => 'Contest',
        'menu_name'          => 'Contest',
        'add_new'            => 'Aggiungi Contest',
        'add_new_item'       => 'Aggiungi Nuovo Contest',
        'edit_item'          => 'Modifica Contest',
        'new_item'           => 'Nuovo Contest',
        'view_item'          => 'Visualizza Contest',
        'search_items'       => 'Cerca Contest',
        'not_found'          => 'Nessun contest trovato',
        'not_found_in_trash' => 'Nessun contest nel cestino',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'contest'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-megaphone',
        'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'        => true,
    );

    register_post_type('contest', $args);
}
add_action('init', 'instacontest_register_contest_cpt');

// ========================================
// 3. FUNZIONI CONTEST
// ========================================

function instacontest_is_contest_active($contest_id) {
    $end_date = get_field('contest_end_date', $contest_id);
    if (!$end_date) {
        return false;
    }
    
    $end_timestamp = false;
    
    if (is_object($end_date) && method_exists($end_date, 'format')) {
        $end_timestamp = $end_date->getTimestamp();
    } else if (is_string($end_date)) {
        $end_timestamp = strtotime($end_date);
    }
    
    if (!$end_timestamp) {
        $end_timestamp = strtotime($end_date);
    }
    
    $current_timestamp = current_time('timestamp');
    return $end_timestamp && $current_timestamp < $end_timestamp;
}

function instacontest_has_winner($contest_id) {
    $winner_username = get_field('winner_username', $contest_id);
    return !empty($winner_username);
}

function instacontest_get_contest_status($contest_id) {
    $is_active = instacontest_is_contest_active($contest_id);
    $has_winner = instacontest_has_winner($contest_id);
    
    if ($is_active) {
        return 'active';
    } elseif (!$has_winner) {
        return 'selecting';
    } else {
        return 'completed';
    }
}

function instacontest_get_active_contests() {
    return get_posts(array(
        'post_type'      => 'contest',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'contest_end_date',
                'value'   => date('Y-m-d H:i:s'),
                'compare' => '>',
                'type'    => 'DATETIME'
            )
        )
    ));
}

function instacontest_get_ended_contests() {
    return get_posts(array(
        'post_type'      => 'contest',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'contest_end_date',
                'value'   => date('Y-m-d H:i:s'),
                'compare' => '<=',
                'type'    => 'DATETIME'
            )
        )
    ));
}

// ========================================
// 4. SISTEMA PUNTEGGI
// ========================================

function instacontest_get_user_points($user_id) {
    $points = get_user_meta($user_id, 'total_points', true);
    return empty($points) ? 0 : intval($points);
}

function instacontest_add_points_to_user($user_id, $points) {
    $current_points = instacontest_get_user_points($user_id);
    $new_total = $current_points + $points;
    update_user_meta($user_id, 'total_points', $new_total);
    return $new_total;
}

function instacontest_get_user_participations($user_id) {
    global $wpdb;
    
    $participations = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->usermeta}
        WHERE user_id = %d
        AND meta_key LIKE 'participated_contest_%'
    ", $user_id));
    
    return intval($participations);
}

function instacontest_get_user_wins($user_id) {
    global $wpdb;
    
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return 0;
    }
    
    $wins = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->postmeta}
        WHERE meta_key = 'winner_username'
        AND (meta_value = %s OR meta_value = %s)
    ", $user->user_login, '@' . $user->user_login));
    
    return intval($wins);
}

// ========================================
// 5. UTILITY FUNCTIONS
// ========================================

function instacontest_format_contest_date($date_string) {
    if (empty($date_string)) {
        return '';
    }
    
    if (is_object($date_string) && method_exists($date_string, 'format')) {
        return $date_string->format('d/m/Y \a\l\l\e H:i');
    }
    
    $timestamp = strtotime($date_string);
    if ($timestamp) {
        return date_i18n('d/m/Y \a\l\l\e H:i', $timestamp);
    }
    
    return $date_string;
}

function instacontest_user_has_participated($user_id, $contest_id) {
    $participated = get_user_meta($user_id, 'participated_contest_' . $contest_id, true);
    return !empty($participated);
}

function instacontest_user_has_won_contest($user_id, $contest_id) {
    $won = get_user_meta($user_id, 'won_contest_' . $contest_id, true);
    return !empty($won);
}

// ========================================
// 6. DATABASE - CREAZIONE TABELLA PARTECIPANTI
// ========================================

function instacontest_create_participants_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'instacontest_participants';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        contest_id bigint(20) NOT NULL,
        nome varchar(100) NOT NULL,
        cognome varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        telefono varchar(20) NOT NULL,
        username_ig varchar(100) NOT NULL,
        has_won tinyint(1) DEFAULT 0,
        check_date datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY contest_id (contest_id),
        KEY username_ig (username_ig)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Crea tabella all'attivazione del tema
add_action('after_switch_theme', 'instacontest_create_participants_table');

function instacontest_save_participant_data($contest_id, $data) {
    global $wpdb;
    
    // Nome tabella per i partecipanti
    $table_name = $wpdb->prefix . 'instacontest_participants';
    
    // Assicurati che la tabella esista
    instacontest_create_participants_table();
    
    // Inserisci dati
    $result = $wpdb->insert(
        $table_name,
        array(
            'contest_id' => $contest_id,
            'nome' => $data['nome'],
            'cognome' => $data['cognome'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'username_ig' => $data['username_ig'],
            'has_won' => $data['has_won'] ? 1 : 0,
            'check_date' => $data['check_date']
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
    );
    
    return $result !== false;
}

// ========================================
// 7. GESTIONE FORM VERIFICA VINCITORE
// ========================================

add_action('init', 'instacontest_process_winner_form');

function instacontest_process_winner_form() {
    // Verifica se il form è stato inviato
    if (!isset($_POST['instacontest_check_winner_nonce']) || 
        !wp_verify_nonce($_POST['instacontest_check_winner_nonce'], 'instacontest_check_winner')) {
        return;
    }
    
    // Verifica che tutti i campi richiesti siano presenti
    if (!isset($_POST['contest_id']) || 
        !isset($_POST['nome']) || 
        !isset($_POST['cognome']) || 
        !isset($_POST['email']) || 
        !isset($_POST['telefono']) || 
        !isset($_POST['username_ig'])) {
        return;
    }
    
    // Sanitizza i dati
    $contest_id = intval($_POST['contest_id']);
    $nome = sanitize_text_field($_POST['nome']);
    $cognome = sanitize_text_field($_POST['cognome']);
    $email = sanitize_email($_POST['email']);
    $telefono = sanitize_text_field($_POST['telefono']);
    $username_ig = sanitize_text_field($_POST['username_ig']);
    
    // Rimuovi @ se presente
    $username_ig = ltrim($username_ig, '@');
    
    // Ottieni username vincitore
    $winner_username = get_field('winner_username', $contest_id);
    $winner_username = ltrim($winner_username, '@');
    
    // Debug per admin
    if (current_user_can('administrator')) {
        error_log("DEBUG Winner Check - Contest: $contest_id, Input: $username_ig, Winner: $winner_username");
    }
    
    // Verifica se ha vinto (case-insensitive)
    $has_won = false;
    if (!empty($winner_username) && !empty($username_ig)) {
        $has_won = (strtolower($username_ig) === strtolower($winner_username));
    }
    
    // Salva dati nel database
    instacontest_save_participant_data($contest_id, array(
        'nome' => $nome,
        'cognome' => $cognome,
        'email' => $email,
        'telefono' => $telefono,
        'username_ig' => $username_ig,
        'has_won' => $has_won,
        'check_date' => current_time('mysql')
    ));
    
    // Gestisci punti se ha vinto
    if ($has_won && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $already_won_key = 'won_contest_' . $contest_id;
        
        if (!get_user_meta($user_id, $already_won_key, true)) {
            $winner_points = get_field('winner_points', $contest_id) ?: 50;
            instacontest_add_points_to_user($user_id, $winner_points);
            update_user_meta($user_id, $already_won_key, time());
            
            $redirect_url = add_query_arg(array(
                'winner_check' => 'won',
                'points_earned' => 'yes'
            ), get_permalink($contest_id));
        } else {
            $redirect_url = add_query_arg(array(
                'winner_check' => 'won',
                'points_earned' => 'already'
            ), get_permalink($contest_id));
        }
    } else {
        $redirect_url = add_query_arg('winner_check', 'lost', get_permalink($contest_id));
    }
    
    wp_redirect($redirect_url);
    exit;
}

// ========================================
// 8. AJAX HANDLER TRACKING PARTECIPAZIONE
// ========================================

add_action('wp_ajax_instacontest_track_participation', 'instacontest_handle_participation');
add_action('wp_ajax_nopriv_instacontest_track_participation', 'instacontest_handle_participation');

function instacontest_handle_participation() {
    // Verifica nonce
    if (!wp_verify_nonce($_POST['nonce'], 'track_participation')) {
        wp_die('Errore di sicurezza');
    }
    
    $contest_id = intval($_POST['contest_id']);
    
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $participation_key = 'participated_contest_' . $contest_id;
        
        if (!get_user_meta($user_id, $participation_key, true)) {
            $participation_points = get_field('participation_points', $contest_id) ?: 5;
            instacontest_add_points_to_user($user_id, $participation_points);
            update_user_meta($user_id, $participation_key, time());
            
            wp_send_json_success(array(
                'message' => 'Punti assegnati!',
                'points' => $participation_points,
                'first_time' => true
            ));
        } else {
            wp_send_json_success(array(
                'message' => 'Già partecipato',
                'points' => 0,
                'first_time' => false
            ));
        }
    }
    
    wp_send_json_success(array(
        'message' => 'Partecipazione registrata',
        'points' => 0,
        'first_time' => false
    ));
}

// ========================================
// 9. THEME SETUP E PERSONALIZZAZIONI
// ========================================

function instacontest_theme_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ));
    add_theme_support('custom-logo');
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'instacontest_theme_setup');

function instacontest_body_class($classes) {
    if (is_post_type_archive('contest') || is_home() || is_front_page()) {
        $classes[] = 'instacontest-homepage';
    } elseif (is_singular('contest')) {
        $classes[] = 'contest-single';
    } elseif (is_page('classifica')) {
        $classes[] = 'classifica-page';
    } elseif (is_page('regolamento')) {
        $classes[] = 'regolamento-page';
    } elseif (is_page('profilo')) {
        $classes[] = 'profilo-page';
    }
    return $classes;
}
add_filter('body_class', 'instacontest_body_class');

// ========================================
// 10. FUNZIONI PLACEHOLDER
// ========================================

function instacontest_get_user_position($user_id) {
    global $wpdb;
    $position = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) + 1 
        FROM {$wpdb->usermeta} um1 
        WHERE um1.meta_key = 'total_points' 
        AND CAST(um1.meta_value AS UNSIGNED) > (
            SELECT CAST(COALESCE(um2.meta_value, '0') AS UNSIGNED) 
            FROM {$wpdb->usermeta} um2 
            WHERE um2.meta_key = 'total_points' 
            AND um2.user_id = %d
        )
    ", $user_id));
    return intval($position);
}

function instacontest_get_top_users($limit = 10) { return array(); }
function instacontest_get_total_participants() { return 0; }
function instacontest_get_total_participations() { return 0; }
function instacontest_get_total_contests() { 
    $total = wp_count_posts('contest');
    return isset($total->publish) ? intval($total->publish) : 0;
}
function instacontest_get_total_prizes_value() { return 0; }
function instacontest_get_user_achievements($user_id) { return array(); }
function instacontest_get_available_achievements() { return array(); }
function instacontest_get_user_contests($user_id) { return array(); }
function instacontest_get_user_notifications($user_id, $unread_only = false) { return array(); }

// ========================================
// 11. FLUSH REWRITE RULES
// ========================================

function instacontest_flush_rewrite_rules() {
    instacontest_register_contest_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'instacontest_flush_rewrite_rules');
