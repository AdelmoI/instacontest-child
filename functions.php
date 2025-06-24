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


// ========================================
// FUNZIONI AGGIORNATE CON CONTEST START DATE
// ========================================

// Verifica se contest è in arrivo (non ancora iniziato)
function instacontest_is_contest_coming($contest_id) {
    $start_date = get_field('contest_start_date', $contest_id);
    if (!$start_date) {
        return false; // Se non ha data inizio, non è "coming soon"
    }
    
    $start_timestamp = strtotime($start_date);
    $current_timestamp = current_time('timestamp');
    
    return $current_timestamp < $start_timestamp;
}

// Ottieni contest in arrivo (coming soon)
function instacontest_get_coming_contests() {
    $contests = get_posts(array(
        'post_type'      => 'contest',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'contest_start_date',
                'value'   => date('Y-m-d H:i:s'),
                'compare' => '>',
                'type'    => 'DATETIME'
            )
        )
    ));
    
    return $contests;
}

// Aggiorna funzione contest attivi per usare start_date
function instacontest_get_active_contests_new() {
    $contests = get_posts(array(
        'post_type'      => 'contest',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'contest_start_date',
                'value'   => date('Y-m-d H:i:s'),
                'compare' => '<=',
                'type'    => 'DATETIME'
            ),
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


//CODICE NUOVO
// ========================================
// SISTEMA GESTIONE UTENTI INSTACONTEST
// ========================================

// 1. CAMPI CUSTOM PER UTENTI
function instacontest_add_user_fields($user) {
    ?>
    <h3>Informazioni InstaContest</h3>
    <table class="form-table">
        <tr>
            <th><label for="instagram_username">Username Instagram *</label></th>
            <td>
                <input type="text" name="instagram_username" id="instagram_username" 
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'instagram_username', true)); ?>" 
                       class="regular-text" placeholder="@tusername" required />
                <p class="description">Username Instagram (obbligatorio per partecipare ai contest)</p>
            </td>
        </tr>
        <tr>
            <th><label for="user_phone">Telefono</label></th>
            <td>
                <input type="tel" name="user_phone" id="user_phone" 
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'user_phone', true)); ?>" 
                       class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="user_city">Città</label></th>
            <td>
                <input type="text" name="user_city" id="user_city" 
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'user_city', true)); ?>" 
                       class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="birth_date">Data di nascita</label></th>
            <td>
                <input type="date" name="birth_date" id="birth_date" 
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'birth_date', true)); ?>" 
                       class="regular-text" />
                <p class="description">Devi essere maggiorenne per partecipare</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'instacontest_add_user_fields');
add_action('edit_user_profile', 'instacontest_add_user_fields');

// 2. SALVA CAMPI CUSTOM
function instacontest_save_user_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Instagram username (obbligatorio)
    if (isset($_POST['instagram_username'])) {
        $instagram_username = sanitize_text_field($_POST['instagram_username']);
        
        // Rimuovi @ se presente all'inizio
        $instagram_username = ltrim($instagram_username, '@');
        
        // Verifica che non sia già usato da altri utenti
        if (!empty($instagram_username)) {
            $existing_user = get_users(array(
                'meta_key' => 'instagram_username',
                'meta_value' => $instagram_username,
                'exclude' => array($user_id)
            ));
            
            if (!empty($existing_user)) {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error"><p>Questo username Instagram è già in uso!</p></div>';
                });
                return false;
            }
            
            update_user_meta($user_id, 'instagram_username', $instagram_username);
        }
    }

    // Altri campi
    if (isset($_POST['user_phone'])) {
        update_user_meta($user_id, 'user_phone', sanitize_text_field($_POST['user_phone']));
    }
    
    if (isset($_POST['user_city'])) {
        update_user_meta($user_id, 'user_city', sanitize_text_field($_POST['user_city']));
    }
    
    if (isset($_POST['birth_date'])) {
        $birth_date = sanitize_text_field($_POST['birth_date']);
        
        // Verifica età minima (18 anni)
        if (!empty($birth_date)) {
            $birth_timestamp = strtotime($birth_date);
            $min_age_timestamp = strtotime('-18 years');
            
            if ($birth_timestamp > $min_age_timestamp) {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error"><p>Devi essere maggiorenne per registrarti!</p></div>';
                });
                return false;
            }
        }
        
        update_user_meta($user_id, 'birth_date', $birth_date);
    }
}
add_action('personal_options_update', 'instacontest_save_user_fields');
add_action('edit_user_profile_update', 'instacontest_save_user_fields');

// 3. FUNZIONI UTILITY PER UTENTI
function instacontest_get_user_instagram($user_id) {
    $username = get_user_meta($user_id, 'instagram_username', true);
    return !empty($username) ? $username : false;
}

function instacontest_user_has_instagram($user_id) {
    return !empty(instacontest_get_user_instagram($user_id));
}

function instacontest_get_user_by_instagram($instagram_username) {
    // Rimuovi @ se presente
    $username = ltrim($instagram_username, '@');
    
    $users = get_users(array(
        'meta_key' => 'instagram_username',
        'meta_value' => $username,
        'number' => 1
    ));
    
    return !empty($users) ? $users[0] : false;
}

// 4. VALIDAZIONE REGISTRAZIONE
function instacontest_validate_registration($errors, $sanitized_user_login, $user_email) {
    // Verifica Instagram username se presente nel form
    if (isset($_POST['instagram_username'])) {
        $instagram_username = sanitize_text_field($_POST['instagram_username']);
        $instagram_username = ltrim($instagram_username, '@');
        
        if (empty($instagram_username)) {
            $errors->add('instagram_username_error', 'L\'username Instagram è obbligatorio.');
        } else {
            // Controlla se esiste già
            $existing_user = instacontest_get_user_by_instagram($instagram_username);
            if ($existing_user) {
                $errors->add('instagram_username_error', 'Questo username Instagram è già registrato.');
            }
            
            // Validazione formato username Instagram
            if (!preg_match('/^[a-zA-Z0-9._]{1,30}$/', $instagram_username)) {
                $errors->add('instagram_username_error', 'Formato username Instagram non valido.');
            }
        }
    }
    
    // Verifica età se presente
    if (isset($_POST['birth_date']) && !empty($_POST['birth_date'])) {
        $birth_date = sanitize_text_field($_POST['birth_date']);
        $birth_timestamp = strtotime($birth_date);
        $min_age_timestamp = strtotime('-18 years');
        
        if ($birth_timestamp > $min_age_timestamp) {
            $errors->add('age_error', 'Devi essere maggiorenne per registrarti.');
        }
    }
    
    return $errors;
}
add_filter('registration_errors', 'instacontest_validate_registration', 10, 3);

// 5. SALVA DATI EXTRA DURANTE REGISTRAZIONE
function instacontest_save_registration_fields($user_id) {
    if (isset($_POST['instagram_username'])) {
        $instagram_username = sanitize_text_field($_POST['instagram_username']);
        $instagram_username = ltrim($instagram_username, '@');
        update_user_meta($user_id, 'instagram_username', $instagram_username);
    }
    
    if (isset($_POST['user_phone'])) {
        update_user_meta($user_id, 'user_phone', sanitize_text_field($_POST['user_phone']));
    }
    
    if (isset($_POST['user_city'])) {
        update_user_meta($user_id, 'user_city', sanitize_text_field($_POST['user_city']));
    }
    
    if (isset($_POST['birth_date'])) {
        update_user_meta($user_id, 'birth_date', sanitize_text_field($_POST['birth_date']));
    }
    
    // Assegna punti di benvenuto
    instacontest_add_points_to_user($user_id, 10);
}
add_action('user_register', 'instacontest_save_registration_fields');

// 6. ABILITA REGISTRAZIONE WORDPRESS
function instacontest_enable_registration() {
    if (!get_option('users_can_register')) {
        update_option('users_can_register', 1);
    }
}
add_action('init', 'instacontest_enable_registration');

// 7. REDIRECT DOPO LOGIN
function instacontest_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('subscriber', $user->roles)) {
            return home_url('/profilo/');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'instacontest_login_redirect', 10, 3);

// 8. CONTROLLO INSTAGRAM OBBLIGATORIO
function instacontest_check_required_instagram() {
    if (is_user_logged_in() && !is_admin()) {
        $user_id = get_current_user_id();
        $instagram = instacontest_get_user_instagram($user_id);
        
        if (empty($instagram) && !is_page('profilo')) {
            // Reindirizza al profilo se manca Instagram
            wp_redirect(home_url('/profilo/?msg=instagram_required'));
            exit;
        }
    }
}
// add_action('template_redirect', 'instacontest_check_required_instagram'); // Decommenta per abilitare


// ========================================
// SISTEMA REDIRECT E LOGIN MANAGEMENT
// ========================================

// 1. REDIRECT LOGIN PERSONALIZZATI
function instacontest_custom_login_redirects() {
    // Reindirizza wp-login.php alla nostra pagina custom
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false && 
        !isset($_GET['action']) && 
        !is_admin()) {
        wp_redirect(home_url('/login/'));
        exit;
    }
    
    // Reindirizza wp-register alla nostra pagina
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php?action=register') !== false) {
        wp_redirect(home_url('/registrazione/'));
        exit;
    }
}
add_action('init', 'instacontest_custom_login_redirects');

// 2. PERSONALIZZA URL WORDPRESS
function instacontest_custom_login_urls($login_url, $redirect, $force_reauth) {
    return home_url('/login/');
}
add_filter('login_url', 'instacontest_custom_login_urls', 10, 3);

function instacontest_custom_register_url($register_url) {
    return home_url('/registrazione/');
}
add_filter('register_url', 'instacontest_custom_register_url');

function instacontest_custom_lostpassword_url($lostpassword_url) {
    return wp_lostpassword_url(); // Mantieni quello di default per ora
}
add_filter('lostpassword_url', 'instacontest_custom_lostpassword_url');

// 3. REDIRECT DOPO LOGOUT
function instacontest_logout_redirect() {
    wp_redirect(home_url('/login/?msg=logout'));
    exit;
}
add_action('wp_logout', 'instacontest_logout_redirect');

// 4. NASCONDI ADMIN BAR PER SUBSCRIBERS
function instacontest_hide_admin_bar($show_admin_bar) {
    if (!current_user_can('edit_posts')) {
        return false;
    }
    return $show_admin_bar;
}
add_filter('show_admin_bar', 'instacontest_hide_admin_bar');

// 5. BLOCCA ACCESSO ADMIN PER SUBSCRIBERS
function instacontest_block_admin_access() {
    if (is_admin() && 
        !defined('DOING_AJAX') && 
        !current_user_can('edit_posts')) {
        wp_redirect(home_url('/profilo/'));
        exit;
    }
}
add_action('admin_init', 'instacontest_block_admin_access');

// 6. AGGIUNGI LINK REGISTRAZIONE AI MENU
function instacontest_add_login_logout_menu($items, $args) {
    if (!is_admin()) {
        if (is_user_logged_in()) {
            $items .= '<li><a href="' . home_url('/profilo/') . '">Profilo</a></li>';
            $items .= '<li><a href="' . wp_logout_url() . '">Logout</a></li>';
        } else {
            $items .= '<li><a href="' . home_url('/login/') . '">Accedi</a></li>';
            $items .= '<li><a href="' . home_url('/registrazione/') . '">Registrati</a></li>';
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'instacontest_add_login_logout_menu', 10, 2);

// 7. PERSONALIZZA MESSAGGI DI ERRORE LOGIN
function instacontest_custom_login_errors($error) {
    global $errors;
    $err_codes = $errors->get_error_codes();
    
    if (in_array('invalid_username', $err_codes)) {
        $error = 'Username non trovato nel sistema.';
    }
    
    if (in_array('incorrect_password', $err_codes)) {
        $error = 'Password non corretta.';
    }
    
    return $error;
}
add_filter('login_errors', 'instacontest_custom_login_errors');

// 8. AGGIUNGI CORPO EMAIL PERSONALIZZATO NUOVI UTENTI
function instacontest_custom_new_user_notification($user_id, $deprecated = null, $notify = '') {
    if ('admin' === $notify || empty($notify)) {
        return;
    }

    $user = get_userdata($user_id);
    $instagram = get_user_meta($user_id, 'instagram_username', true);
    
    $message = sprintf(
        "Ciao %s!\n\n" .
        "Benvenuto in InstaContest! 🎯\n\n" .
        "Il tuo account è stato creato con successo:\n" .
        "Username: %s\n" .
        "Instagram: @%s\n\n" .
        "Hai ricevuto 10 punti di benvenuto!\n\n" .
        "Inizia subito a partecipare ai contest: %s\n\n" .
        "Buona fortuna!\n" .
        "Il Team InstaContest",
        $user->first_name,
        $user->user_login,
        $instagram,
        home_url()
    );
    
    wp_mail($user->user_email, 'Benvenuto in InstaContest! 🎯', $message);
}
add_action('wp_new_user_notification_email_admin', 'instacontest_custom_new_user_notification', 10, 3);

// 9. FORZA HTTPS PER LOGIN (se il sito usa HTTPS)
function instacontest_force_ssl_login($force_ssl, $user_id, $secure) {
    return true;
}
if (is_ssl()) {
    add_filter('force_ssl_admin', 'instacontest_force_ssl_login', 10, 3);
}

// 10. NOTIFICA ADMIN PER NUOVE REGISTRAZIONI
function instacontest_notify_admin_new_user($user_id) {
    $user = get_userdata($user_id);
    $instagram = get_user_meta($user_id, 'instagram_username', true);
    
    $message = sprintf(
        "Nuovo utente registrato in InstaContest:\n\n" .
        "Nome: %s %s\n" .
        "Username: %s\n" .
        "Email: %s\n" .
        "Instagram: @%s\n" .
        "Data registrazione: %s\n\n" .
        "Visualizza profilo: %s",
        $user->first_name,
        $user->last_name,
        $user->user_login,
        $user->user_email,
        $instagram,
        date('d/m/Y H:i'),
        admin_url('user-edit.php?user_id=' . $user_id)
    );
    
    wp_mail(get_option('admin_email'), 'Nuova registrazione InstaContest', $message);
}
add_action('user_register', 'instacontest_notify_admin_new_user', 11);
