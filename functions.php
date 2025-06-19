<?php
/**
 * INSTACONTEST CHILD THEME - FUNCTIONS.PHP COMPLETO
 * Tutte le funzioni necessarie per i template - prova test2
 */

// Previeni accesso diretto
if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// 1. ENQUEUE STYLES E SCRIPTS
// ========================================

function instacontest_enqueue_styles() {
    // Carica il CSS del tema parent (Astra)
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    
    // Carica il CSS del child theme
    wp_enqueue_style('instacontest-child-style', 
        get_stylesheet_directory_uri() . '/style.css',
        array('astra-parent-style'),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'instacontest_enqueue_styles');

// ========================================
// 2. REGISTRAZIONE CUSTOM POST TYPE
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
// 3. FUNZIONI CONTEST BASE
// ========================================

// SOSTITUISCI la funzione instacontest_is_contest_active con questa:
function instacontest_is_contest_active($contest_id) {
    $end_date = get_field('contest_end_date', $contest_id);
    if (!$end_date) {
        return false;
    }
    
    // Debug: mostra le date (rimuovere dopo fix)
    if (current_user_can('administrator')) {
        echo "<!-- DEBUG Contest ID: $contest_id -->";
        echo "<!-- End Date from ACF: $end_date -->";
        echo "<!-- Current Time: " . current_time('Y-m-d H:i:s') . " -->";
        echo "<!-- Timezone: " . get_option('timezone_string') . " -->";
    }
    
    // Converti data in timestamp usando timezone WordPress
    $end_timestamp = strtotime($end_date . ' ' . get_option('timezone_string'));
    $current_timestamp = current_time('timestamp');
    
    // Debug timestamps
    if (current_user_can('administrator')) {
        echo "<!-- End Timestamp: $end_timestamp -->";
        echo "<!-- Current Timestamp: $current_timestamp -->";
        echo "<!-- Is Active: " . ($current_timestamp < $end_timestamp ? 'YES' : 'NO') . " -->";
    }
    
    return $current_timestamp < $end_timestamp;
}

// Verifica se contest è terminato
function instacontest_is_contest_ended($contest_id) {
    return !instacontest_is_contest_active($contest_id);
}

// Verifica se contest ha un vincitore
function instacontest_has_winner($contest_id) {
    $winner_username = get_field('winner_username', $contest_id);
    return !empty($winner_username);
}

// Ottieni stato contest
function instacontest_get_contest_status($contest_id) {
    $is_active = instacontest_is_contest_active($contest_id);
    $has_winner = instacontest_has_winner($contest_id);
    
    if ($is_active) {
        return 'active'; // Contest attivo
    } elseif (!$has_winner) {
        return 'selecting'; // Contest finito, selezione vincitore in corso
    } else {
        return 'completed'; // Contest finito con vincitore
    }
}

// ========================================
// 4. FUNZIONI PER OTTENERE CONTEST
// ========================================

// Ottieni contest attivi
function instacontest_get_active_contests() {
    $contests = get_posts(array(
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
    
    return $contests;
}

// Ottieni contest terminati
function instacontest_get_ended_contests() {
    $contests = get_posts(array(
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
    
    return $contests;
}

// ========================================
// 5. SISTEMA PUNTEGGI
// ========================================

// Ottieni punti utente
function instacontest_get_user_points($user_id) {
    $points = get_user_meta($user_id, 'total_points', true);
    return empty($points) ? 0 : intval($points);
}

// Aggiungi punti a utente
function instacontest_add_points_to_user($user_id, $points) {
    $current_points = instacontest_get_user_points($user_id);
    $new_total = $current_points + $points;
    update_user_meta($user_id, 'total_points', $new_total);
    return $new_total;
}

// Ottieni posizione utente in classifica
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

// Ottieni numero partecipazioni utente
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

// Ottieni numero vittorie utente
function instacontest_get_user_wins($user_id) {
    global $wpdb;
    
    // Ottieni username dell'utente
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return 0;
    }
    
    // Conta i contest dove l'utente è il vincitore
    $wins = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->postmeta}
        WHERE meta_key = 'winner_username'
        AND (meta_value = %s OR meta_value = %s)
    ", $user->user_login, '@' . $user->user_login));
    
    return intval($wins);
}

// ========================================
// 6. UTILITY FUNCTIONS
// ========================================

// Formatta data contest italiana
function instacontest_format_contest_date($date_string) {
    if (empty($date_string)) {
        return '';
    }
    
    return date_i18n('d/m/Y \a\l\l\e H:i', strtotime($date_string));
}

// Verifica se utente ha già partecipato
function instacontest_user_has_participated($user_id, $contest_id) {
    $participated = get_user_meta($user_id, 'participated_contest_' . $contest_id, true);
    return !empty($participated);
}

// ========================================
// 7. FUNZIONI PER I TEMPLATE (PLACEHOLDER)
// ========================================

// Funzioni placeholder per evitare errori - sostituire con logica reale
function instacontest_get_top_users($limit = 10) {
    // Placeholder - ritorna array vuoto per ora
    return array();
}

function instacontest_get_total_participants() {
    return 0;
}

function instacontest_get_total_participations() {
    return 0;
}

function instacontest_get_total_contests() {
    $total = wp_count_posts('contest');
    return isset($total->publish) ? intval($total->publish) : 0;
}

function instacontest_get_total_prizes_value() {
    return 0;
}

function instacontest_get_user_achievements($user_id) {
    return array();
}

function instacontest_get_available_achievements() {
    return array();
}

function instacontest_get_user_contests($user_id) {
    return array();
}

function instacontest_get_user_notifications($user_id, $unread_only = false) {
    return array();
}

// ========================================
// 8. FLUSH REWRITE RULES
// ========================================

function instacontest_flush_rewrite_rules() {
    instacontest_register_contest_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'instacontest_flush_rewrite_rules');

