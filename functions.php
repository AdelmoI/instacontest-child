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


// ========================================
// AGGIUNGI QUESTE FUNZIONI AL TUO FUNCTIONS.PHP
// ========================================

// ========================================
// 11. GESTIONE UTENTI E REGISTRAZIONE
// ========================================

// Redirect dopo login/registrazione
function instacontest_login_redirect($redirect_to, $request, $user) {
    // Se è un admin, vai alla dashboard
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            return admin_url();
        }
    }
    
    // Utenti normali vanno al profilo
    return home_url('/profilo');
}
add_filter('login_redirect', 'instacontest_login_redirect', 10, 3);

// Redirect dopo logout
function instacontest_logout_redirect() {
    wp_redirect(home_url());
    exit();
}
add_action('wp_logout', 'instacontest_logout_redirect');

// ========================================
// 12. CREAZIONE PAGINE AUTOMATICA
// ========================================

// Crea le pagine necessarie se non esistono
function instacontest_create_user_pages() {
    
    // Pagina Login
    if (!get_page_by_path('login')) {
        wp_insert_post(array(
            'post_title'     => 'Login',
            'post_name'      => 'login',
            'post_content'   => '[instacontest_login_form]',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'meta_input'     => array(
                '_wp_page_template' => 'page-login.php'
            )
        ));
    }
    
    // Pagina Registrazione
    if (!get_page_by_path('register')) {
        wp_insert_post(array(
            'post_title'     => 'Registrazione',
            'post_name'      => 'register',
            'post_content'   => '[instacontest_register_form]',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'meta_input'     => array(
                '_wp_page_template' => 'page-register.php'
            )
        ));
    }
}

// Esegui al cambio di tema
add_action('after_switch_theme', 'instacontest_create_user_pages');

// ========================================
// 13. UTILITY FUNCTIONS PER UTENTI
// ========================================

// Ottieni username Instagram di un utente
function instacontest_get_user_instagram($user_id) {
    $instagram = get_user_meta($user_id, 'instagram_username', true);
    return !empty($instagram) ? $instagram : '';
}

// Verifica se utente ha username Instagram
function instacontest_user_has_instagram($user_id) {
    $instagram = instacontest_get_user_instagram($user_id);
    return !empty($instagram);
}

// Ottieni avatar personalizzato o default
function instacontest_get_user_avatar($user_id, $size = 96) {
    $avatar = get_avatar($user_id, $size, '', '', array('class' => 'rounded-full'));
    return $avatar;
}

// ========================================
// 14. SICUREZZA E VALIDAZIONI
// ========================================

// Validazione username Instagram
function instacontest_validate_instagram_username($username) {
    // Rimuovi @ se presente
    $username = ltrim($username, '@');
    
    // Controlli base
    if (empty($username)) {
        return false;
    }
    
    if (strlen($username) < 1 || strlen($username) > 30) {
        return false;
    }
    
    // Solo caratteri alfanumerici, underscore e punto
    if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
        return false;
    }
    
    return true;
}

// Controlla se username Instagram è già in uso
function instacontest_instagram_username_exists($username, $exclude_user_id = 0) {
    global $wpdb;
    
    $username = ltrim($username, '@');
    
    $query = $wpdb->prepare(
        "SELECT user_id FROM {$wpdb->usermeta} 
         WHERE meta_key = 'instagram_username' 
         AND meta_value = %s",
        $username
    );
    
    if ($exclude_user_id > 0) {
        $query .= $wpdb->prepare(" AND user_id != %d", $exclude_user_id);
    }
    
    $result = $wpdb->get_var($query);
    return !empty($result);
}

// ========================================
// 15. SHORTCODES PER FORM (opzionali)
// ========================================

// Shortcode per form login
function instacontest_login_form_shortcode() {
    ob_start();
    get_template_part('user-templates/login-form');
    return ob_get_clean();
}
add_shortcode('instacontest_login_form', 'instacontest_login_form_shortcode');

// Shortcode per form registrazione
function instacontest_register_form_shortcode() {
    ob_start();
    get_template_part('user-templates/register-form');
    return ob_get_clean();
}
add_shortcode('instacontest_register_form', 'instacontest_register_form_shortcode');

// ========================================
// 16. PERSONALIZZAZIONI PROFILO ADMIN
// ========================================

// Aggiungi campo Instagram nel profilo admin
function instacontest_add_instagram_field($user) {
    $instagram = get_user_meta($user->ID, 'instagram_username', true);
    ?>
    <h3>Informazioni InstaContest</h3>
    <table class="form-table">
        <tr>
            <th><label for="instagram_username">Username Instagram</label></th>
            <td>
                <input type="text" name="instagram_username" id="instagram_username" 
                       value="<?php echo esc_attr($instagram); ?>" class="regular-text" />
                <p class="description">Il tuo username Instagram (senza @)</p>
            </td>
        </tr>
        <tr>
            <th><label for="total_points">Punti Totali</label></th>
            <td>
                <input type="number" name="total_points" id="total_points" 
                       value="<?php echo instacontest_get_user_points($user->ID); ?>" class="regular-text" />
                <p class="description">Punti accumulati dall'utente</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'instacontest_add_instagram_field');
add_action('edit_user_profile', 'instacontest_add_instagram_field');

// Salva campo Instagram nel profilo admin
function instacontest_save_instagram_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    if (isset($_POST['instagram_username'])) {
        $instagram = sanitize_text_field($_POST['instagram_username']);
        $instagram = ltrim($instagram, '@');
        update_user_meta($user_id, 'instagram_username', $instagram);
    }
    
    if (isset($_POST['total_points']) && current_user_can('manage_options')) {
        $points = intval($_POST['total_points']);
        update_user_meta($user_id, 'total_points', $points);
    }
}
add_action('personal_options_update', 'instacontest_save_instagram_field');
add_action('edit_user_profile_update', 'instacontest_save_instagram_field');


// ========================================
// AGGIUNGI AL FUNCTIONS.PHP - SISTEMA AVATAR PERSONALIZZATO
// ========================================

// Override avatar di WordPress con avatar personalizzato
function instacontest_custom_avatar($avatar, $id_or_email, $size, $default, $alt, $args) {
    // Ottieni user ID
    $user_id = false;
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if ($user) {
            $user_id = $user->ID;
        }
    } elseif ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    }
    
    if ($user_id) {
        $custom_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
        if ($custom_avatar_id) {
            $custom_avatar_url = wp_get_attachment_image_url($custom_avatar_id, 'thumbnail');
            if ($custom_avatar_url) {
                $class = isset($args['class']) ? $args['class'] : 'avatar avatar-' . $size . ' photo';
                return '<img alt="' . esc_attr($alt) . '" src="' . esc_url($custom_avatar_url) . '" class="' . esc_attr($class) . '" height="' . esc_attr($size) . '" width="' . esc_attr($size) . '" style="object-fit: cover;" />';
            }
        }
    }
    
    return $avatar;
}
add_filter('get_avatar', 'instacontest_custom_avatar', 10, 6);

// Funzione per ottenere URL avatar personalizzato
function instacontest_get_user_avatar_url($user_id, $size = 'thumbnail') {
    $custom_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
    if ($custom_avatar_id) {
        return wp_get_attachment_image_url($custom_avatar_id, $size);
    }
    return false;
}

// Pulizia attachment quando si cambia avatar
function instacontest_cleanup_old_avatar($user_id, $new_avatar_id) {
    $old_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
    
    if ($old_avatar_id && $old_avatar_id != $new_avatar_id) {
        // Elimina vecchio attachment
        wp_delete_attachment($old_avatar_id, true);
    }
}

// Hook per pulizia avatar
add_action('update_user_meta', function($meta_id, $user_id, $meta_key, $meta_value) {
    if ($meta_key === 'custom_avatar' && $meta_value) {
        instacontest_cleanup_old_avatar($user_id, $meta_value);
    }
}, 10, 4);

// Aggiungi campo avatar nel profilo admin
function instacontest_add_avatar_field_admin($user) {
    $custom_avatar_id = get_user_meta($user->ID, 'custom_avatar', true);
    $avatar_url = $custom_avatar_id ? wp_get_attachment_image_url($custom_avatar_id, 'thumbnail') : '';
    ?>
    <h3>Avatar Personalizzato</h3>
    <table class="form-table">
        <tr>
            <th><label for="custom_avatar_preview">Avatar Attuale</label></th>
            <td>
                <div style="margin-bottom: 10px;">
                    <?php if ($avatar_url): ?>
                        <img src="<?php echo esc_url($avatar_url); ?>" style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover;" />
                        <p class="description">Avatar personalizzato caricato dal profilo frontend</p>
                    <?php else: ?>
                        <?php echo get_avatar($user->ID, 96); ?>
                        <p class="description">Avatar predefinito WordPress/Gravatar</p>
                    <?php endif; ?>
                </div>
                <?php if ($custom_avatar_id): ?>
                    <button type="button" onclick="if(confirm('Sei sicuro di voler rimuovere l\'avatar personalizzato?')) { document.getElementById('remove_custom_avatar').value='1'; this.form.submit(); }" class="button">
                        Rimuovi Avatar Personalizzato
                    </button>
                    <input type="hidden" id="remove_custom_avatar" name="remove_custom_avatar" value="0">
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'instacontest_add_avatar_field_admin');
add_action('edit_user_profile', 'instacontest_add_avatar_field_admin');

// Salva rimozione avatar da admin
function instacontest_save_avatar_admin($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    if (isset($_POST['remove_custom_avatar']) && $_POST['remove_custom_avatar'] == '1') {
        $old_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
        if ($old_avatar_id) {
            wp_delete_attachment($old_avatar_id, true);
            delete_user_meta($user_id, 'custom_avatar');
        }
    }
}
add_action('personal_options_update', 'instacontest_save_avatar_admin');
add_action('edit_user_profile_update', 'instacontest_save_avatar_admin');


// ========================================
// FUNZIONE GESTIONE FORM VERIFICA VINCITORE
// Aggiungi questa sezione al tuo functions.php
// ========================================

// Hook per processare il form quando viene inviato
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
    
    // Rimuovi @ se presente all'inizio dello username
    $username_ig = ltrim($username_ig, '@');
    
    // Ottieni l'username vincitore dal contest
    $winner_username = get_field('winner_username', $contest_id);
    
    // Rimuovi @ anche dall'username vincitore se presente
    $winner_username = ltrim($winner_username, '@');
    
    // Debug per admin (rimuovere in produzione)
    if (current_user_can('administrator')) {
        error_log("DEBUG Contest ID: $contest_id");
        error_log("DEBUG Username inserito: $username_ig");
        error_log("DEBUG Username vincitore: $winner_username");
    }
    
    // Verifica se ha vinto (case-insensitive)
    $has_won = false;
    if (!empty($winner_username) && !empty($username_ig)) {
        $has_won = (strtolower($username_ig) === strtolower($winner_username));
    }
    
    // Salva i dati del partecipante nel database (opzionale)
    instacontest_save_participant_data($contest_id, array(
        'nome' => $nome,
        'cognome' => $cognome,
        'email' => $email,
        'telefono' => $telefono,
        'username_ig' => $username_ig,
        'has_won' => $has_won,
        'check_date' => current_time('mysql')
    ));
    
   // Se ha vinto e è loggato, aggiungi punti extra SOLO LA PRIMA VOLTA
    if ($has_won && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $points_already_awarded = get_user_meta($user_id, 'won_contest_' . $contest_id, true);
        
        // Controlla se non ha già ricevuto i punti per questo contest
        if (!$points_already_awarded) {
            $winner_points = get_field('winner_points', $contest_id) ?: 50;
            instacontest_add_points_to_user($user_id, $winner_points);
            
            // Marca che ha ricevuto i punti per questo contest
            update_user_meta($user_id, 'won_contest_' . $contest_id, true);
            
            // Redirect con flag "nuovi punti"
            $redirect_url = get_permalink($contest_id);
            $redirect_url = add_query_arg(array(
                'winner_check' => 'won',
                'new_points' => '1'
            ), $redirect_url);
            
            // Debug per admin
            if (current_user_can('administrator')) {
                error_log("DEBUG: Punti vittoria assegnati ($winner_points) per contest $contest_id all'utente $user_id");
            }
        } else {
            // Redirect normale (punti già ricevuti)
            $redirect_url = get_permalink($contest_id);
            $redirect_url = add_query_arg('winner_check', 'won', $redirect_url);
            
            // Debug per admin
            if (current_user_can('administrator')) {
                error_log("DEBUG: Punti vittoria già assegnati per contest $contest_id all'utente $user_id");
            }
        }
    } else {
        // Non ha vinto o non è loggato
        $redirect_url = get_permalink($contest_id);
        $result = $has_won ? 'won' : 'lost';
        $redirect_url = add_query_arg('winner_check', $result, $redirect_url);
    }
    
    // Redirect con risultato
    wp_redirect($redirect_url);
    exit;
}

// ========================================
// FUNZIONE SALVATAGGIO DATI PARTECIPANTI
// ========================================

function instacontest_save_participant_data($contest_id, $data) {
    global $wpdb;
    
    // Nome tabella per i partecipanti
    $table_name = $wpdb->prefix . 'instacontest_participants';
    
    // Crea tabella se non esiste
    instacontest_create_participants_table();
    
    // Inserisci dati
    $wpdb->insert(
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
}

// ========================================
// CREAZIONE TABELLA PARTECIPANTI
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

// ========================================
// FUNZIONI UTILITY PER DEBUG
// ========================================

// Funzione per testare se un contest ha vincitore
function instacontest_debug_contest_winner($contest_id) {
    if (!current_user_can('administrator')) {
        return;
    }
    
    $winner_username = get_field('winner_username', $contest_id);
    $status = instacontest_get_contest_status($contest_id);
    
    echo "<!-- DEBUG Contest $contest_id -->";
    echo "<!-- Winner Username: '$winner_username' -->";
    echo "<!-- Status: '$status' -->";
    echo "<!-- Has Winner: " . (instacontest_has_winner($contest_id) ? 'YES' : 'NO') . " -->";
}


// ========================================
// HANDLER AJAX PER TRACKING PARTECIPAZIONE E PUNTI
// Aggiungi questa sezione al tuo functions.php
// ========================================

// Handler AJAX per utenti loggati
add_action('wp_ajax_instacontest_track_participation', 'instacontest_handle_participation');

// Handler AJAX per utenti non loggati (opzionale)
add_action('wp_ajax_nopriv_instacontest_track_participation', 'instacontest_handle_participation');

function instacontest_handle_participation() {
    // Verifica nonce per sicurezza
    if (!wp_verify_nonce($_POST['nonce'], 'instacontest_nonce')) {
        wp_die('Errore di sicurezza');
    }
    
    // Verifica se contest_id è presente
    if (!isset($_POST['contest_id']) || empty($_POST['contest_id'])) {
        wp_die('Contest ID mancante');
    }
    
    $contest_id = intval($_POST['contest_id']);
    
    // Se utente non è loggato, termina qui (nessun punto)
    if (!is_user_logged_in()) {
        wp_send_json_success(array(
            'message' => 'Partecipazione registrata (ospite)',
            'points_awarded' => 0
        ));
        return;
    }
    
    $user_id = get_current_user_id();
    
    // Verifica se ha già partecipato a questo contest
    $already_participated = instacontest_user_has_participated($user_id, $contest_id);
    
    if (!$already_participated) {
        // PRIMA PARTECIPAZIONE - Assegna punti
        
        // Ottieni punti partecipazione dal contest
        $participation_points = get_field('participation_points', $contest_id) ?: 5;
        
        // Aggiungi punti all'utente
        $new_total = instacontest_add_points_to_user($user_id, $participation_points);
        
        // Marca come partecipato
        update_user_meta($user_id, 'participated_contest_' . $contest_id, true);
        
        // Debug per admin
        if (current_user_can('administrator')) {
            error_log("DEBUG: Punti partecipazione assegnati ($participation_points) per contest $contest_id all'utente $user_id");
        }
        
        wp_send_json_success(array(
            'message' => 'Partecipazione registrata! Hai guadagnato ' . $participation_points . ' punti!',
            'points_awarded' => $participation_points,
            'new_total' => $new_total,
            'first_time' => true
        ));
        
    } else {
        // GIÀ PARTECIPATO - Nessun punto extra
        
        // Debug per admin
        if (current_user_can('administrator')) {
            error_log("DEBUG: Utente $user_id ha già partecipato al contest $contest_id");
        }
        
        wp_send_json_success(array(
            'message' => 'Hai già partecipato a questo contest',
            'points_awarded' => 0,
            'first_time' => false
        ));
    }
}
