<?php
/**
 * INSTACONTEST CHILD THEME - FUNCTIONS.PHP PULITO
 * Tutto consolidato e semplificato
 */

// Previeni accesso diretto
if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// 1. ENQUEUE STYLES E SCRIPTS - SEMPLIFICATO
// ========================================

function instacontest_enqueue_styles() {
    // 1. Tailwind CSS (prima di tutto per avere base)
    wp_enqueue_style('tailwindcss', 
        'https://cdn.tailwindcss.com', 
        array(), 
        '3.3.0'
    );
    
    // 2. FontAwesome
    wp_enqueue_style('fontawesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', 
        array(), 
        '6.4.0'
    );
    
    // 3. Tema parent Astra
    wp_enqueue_style('astra-parent-style', 
        get_template_directory_uri() . '/style.css',
        array('tailwindcss')
    );
    
    // 4. Child theme CSS (DOPO tutto per avere priorità massima)
    wp_enqueue_style('instacontest-style', 
        get_stylesheet_directory_uri() . '/style.css',
        array('astra-parent-style', 'tailwindcss', 'fontawesome'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'instacontest_enqueue_styles');

function instacontest_enqueue_scripts() {
    // Tailwind Config inline
    wp_add_inline_script('tailwindcss', '
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#667eea",
                        secondary: "#f5576c", 
                        success: "#4facfe",
                        warning: "#43e97b"
                    }
                }
            }
        }
    ');
    
    // JavaScript personalizzato (opzionale)
    wp_enqueue_script('instacontest-js', 
        get_stylesheet_directory_uri() . '/instacontest.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // AJAX localization
    wp_localize_script('instacontest-js', 'instacontest_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('instacontest_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'instacontest_enqueue_scripts');

// ========================================
// 2. BODY CLASS PER CSS SPECIFICITY
// ========================================

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
// 3. THEME SETUP
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

// ========================================
// 4. PERSONALIZZAZIONI ASTRA
// ========================================

// Disabilita layout Astra per le nostre pagine
add_filter('astra_page_layout', 'instacontest_disable_astra_layout');
function instacontest_disable_astra_layout($layout) {
    if (is_post_type_archive('contest') || is_singular('contest') || 
        is_page_template('page-classifica.php') || 
        is_page_template('page-regolamento.php') || 
        is_page_template('page-profilo.php')) {
        return 'no-sidebar';
    }
    return $layout;
}

// Disabilita breadcrumbs
add_filter('astra_disable_breadcrumbs', 'instacontest_disable_breadcrumbs');
function instacontest_disable_breadcrumbs($disable) {
    if (is_post_type_archive('contest') || is_singular('contest') || 
        is_page_template('page-classifica.php') || 
        is_page_template('page-regolamento.php') || 
        is_page_template('page-profilo.php')) {
        return true;
    }
    return $disable;
}

// ========================================
// 5. CUSTOM POST TYPE CONTEST
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
// 6. FUNZIONI CONTEST
// ========================================

// Verifica se contest è attivo
function instacontest_is_contest_active($contest_id) {
    $end_date = get_field('contest_end_date', $contest_id);
    if (!$end_date) {
        return false;
    }
    
    $end_timestamp = false;
    
    if (is_object($end_date) && method_exists($end_date, 'format')) {
        $end_timestamp = $end_date->getTimestamp();
    } else if (is_string($end_date)) {
        $date_clean = $end_date;
        if (strpos($end_date, 'am') !== false || strpos($end_date, 'pm') !== false) {
            $date_clean = date('Y-m-d H:i:s', strtotime($end_date));
        }
        $end_timestamp = strtotime($date_clean);
    }
    
    if (!$end_timestamp) {
        $end_timestamp = strtotime($end_date);
    }
    
    $current_timestamp = current_time('timestamp');
    return $end_timestamp && $current_timestamp < $end_timestamp;
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
        return 'active';
    } elseif (!$has_winner) {
        return 'selecting';
    } else {
        return 'completed';
    }
}

// Ottieni contest attivi
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

// Ottieni contest terminati
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
// 7. SISTEMA PUNTEGGI
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
// 8. UTILITY FUNCTIONS
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

// ========================================
// 9. FUNZIONI PLACEHOLDER (per evitare errori)
// ========================================

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
// 10. FLUSH REWRITE RULES
// ========================================

function instacontest_flush_rewrite_rules() {
    instacontest_register_contest_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'instacontest_flush_rewrite_rules');
