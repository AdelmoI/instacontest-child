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


/**
 * FUNZIONI AGGIUNTIVE PER GESTIONE VINCITORE CONTEST
 * Aggiungi queste funzioni al tuo functions.php
 */

// ========================================
// GESTIONE VERIFICA VINCITORE
// ========================================

function instacontest_handle_winner_check() {
    // Verifica se è stata inviata la form di verifica vincitore
    if (isset($_POST['instacontest_check_winner_nonce']) && 
        wp_verify_nonce($_POST['instacontest_check_winner_nonce'], 'instacontest_check_winner')) {
        
        $contest_id = intval($_POST['contest_id']);
        $nome = sanitize_text_field($_POST['nome']);
        $cognome = sanitize_text_field($_POST['cognome']);
        $email = sanitize_email($_POST['email']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $username_ig = sanitize_text_field($_POST['username_ig']);
        
        // Rimuovi @ se presente all'inizio
        $username_ig = ltrim($username_ig, '@');
        
        // Ottieni username vincitore dal contest
        $winner_username = get_field('winner_username', $contest_id);
        $winner_username = ltrim($winner_username, '@'); // Rimuovi @ se presente
        
        // Verifica se l'username corrisponde
        $is_winner = false;
        if (!empty($winner_username) && !empty($username_ig)) {
            $is_winner = (strtolower($username_ig) === strtolower($winner_username));
        }
        
        if ($is_winner) {
            // VINCITORE!
            
            // Salva i dati del vincitore
            $winner_data = array(
                'nome' => $nome,
                'cognome' => $cognome,
                'email' => $email,
                'telefono' => $telefono,
                'username_ig' => $username_ig,
                'contest_id' => $contest_id,
                'verification_date' => current_time('mysql')
            );
            
            // Salva i dati come meta del contest
            update_post_meta($contest_id, 'winner_verified_data', $winner_data);
            
            // Se l'utente è loggato, aggiungi punti bonus
            if (is_user_logged_in()) {
                $winner_points = get_field('winner_points', $contest_id) ?: 50;
                instacontest_add_points_to_user(get_current_user_id(), $winner_points);
                
                // Registra la vittoria
                update_user_meta(get_current_user_id(), 'won_contest_' . $contest_id, true);
            }
            
            // Log della vittoria (opzionale)
            error_log("InstaContest: Vincitore verificato per contest {$contest_id}: {$username_ig}");
            
            // Redirect con messaggio di successo
            wp_redirect(add_query_arg('winner_check', 'won', get_permalink($contest_id)));
            exit;
            
        } else {
            // NON VINCITORE
            wp_redirect(add_query_arg('winner_check', 'lost', get_permalink($contest_id)));
            exit;
        }
    }
}
add_action('init', 'instacontest_handle_winner_check');

// ========================================
// AJAX TRACKING PARTECIPAZIONE
// ========================================

function instacontest_ajax_track_participation() {
    // Verifica nonce
    if (!wp_verify_nonce($_POST['nonce'], 'track_participation')) {
        wp_die('Nonce verification failed');
    }
    
    // Verifica se utente è loggato
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
        return;
    }
    
    $contest_id = intval($_POST['contest_id']);
    $user_id = get_current_user_id();
    
    // Verifica se l'utente ha già partecipato
    $already_participated = get_user_meta($user_id, 'participated_contest_' . $contest_id, true);
    
    if (!$already_participated) {
        // Registra la partecipazione
        update_user_meta($user_id, 'participated_contest_' . $contest_id, true);
        
        // Aggiungi punti per la partecipazione
        $participation_points = get_field('participation_points', $contest_id) ?: 5;
        $new_total = instacontest_add_points_to_user($user_id, $participation_points);
        
        wp_send_json_success(array(
            'message' => 'Partecipazione registrata!',
            'points_earned' => $participation_points,
            'total_points' => $new_total
        ));
    } else {
        wp_send_json_success(array(
            'message' => 'Partecipazione già registrata',
            'points_earned' => 0
        ));
    }
}
add_action('wp_ajax_instacontest_track_participation', 'instacontest_ajax_track_participation');

// ========================================
// FUNZIONI UTILITY PER VINCITORE
// ========================================

// Ottieni dati del vincitore verificato
function instacontest_get_winner_data($contest_id) {
    return get_post_meta($contest_id, 'winner_verified_data', true);
}

// Verifica se vincitore è stato verificato
function instacontest_is_winner_verified($contest_id) {
    $winner_data = instacontest_get_winner_data($contest_id);
    return !empty($winner_data);
}

// Ottieni statistiche partecipanti per contest
function instacontest_get_contest_participants_count($contest_id) {
    global $wpdb;
    
    $participants = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->usermeta}
        WHERE meta_key = %s
    ", 'participated_contest_' . $contest_id));
    
    return intval($participants);
}

// ========================================
// FUNZIONI NOTIFICHE (OPZIONALE)
// ========================================

function instacontest_send_winner_notification($contest_id) {
    $winner_data = instacontest_get_winner_data($contest_id);
    if (!$winner_data) {
        return false;
    }
    
    $prize_name = get_field('prize_name', $contest_id);
    $contest_title = get_the_title($contest_id);
    
    // Prepara email per il vincitore
    $to = $winner_data['email'];
    $subject = 'Congratulazioni! Hai vinto il contest InstaContest';
    $message = "
    Ciao {$winner_data['nome']},
    
    Congratulazioni! Hai vinto il contest '{$contest_title}'!
    
    Premio: {$prize_name}
    
    Ti contatteremo presto per organizzare la consegna del premio.
    
    Grazie per aver partecipato a InstaContest!
    ";
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    return wp_mail($to, $subject, $message, $headers);
}

// ========================================
// SHORTCODES UTILI
// ========================================

// Shortcode per mostrare contatore partecipanti
function instacontest_participants_counter_shortcode($atts) {
    $atts = shortcode_atts(array(
        'contest_id' => get_the_ID(),
        'show_avatars' => 'true'
    ), $atts);
    
    $contest_id = intval($atts['contest_id']);
    $participants_count = instacontest_get_contest_participants_count($contest_id);
    
    // Se non ci sono partecipanti registrati, usa un numero simulato
    if ($participants_count === 0) {
        $participants_count = rand(150, 500);
    }
    
    ob_start();
    ?>
    <div class="participants-counter-widget">
        <?php if ($atts['show_avatars'] === 'true'): ?>
            <div class="avatar-group">
                <div class="participant-avatar">😊</div>
                <div class="participant-avatar">🤩</div>
                <div class="participant-avatar">😍</div>
                <div class="participant-avatar">+<?php echo rand(10, 99); ?></div>
            </div>
        <?php endif; ?>
        <div class="participants-info">
            <strong><?php echo number_format($participants_count, 0, ',', '.'); ?></strong>
            <span>persone partecipano</span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('instacontest_participants', 'instacontest_participants_counter_shortcode');

// Shortcode per countdown contest
function instacontest_countdown_shortcode($atts) {
    $atts = shortcode_atts(array(
        'contest_id' => get_the_ID(),
        'style' => 'compact'
    ), $atts);
    
    $contest_id = intval($atts['contest_id']);
    $end_date = get_field('contest_end_date', $contest_id);
    
    if (!$end_date) {
        return '<p>Data di scadenza non impostata.</p>';
    }
    
    $contest_active = instacontest_is_contest_active($contest_id);
    
    if (!$contest_active) {
        return '<p class="contest-ended">Contest terminato</p>';
    }
    
    ob_start();
    ?>
    <div class="instacontest-countdown-widget" 
         data-end-date="<?php echo date('Y-m-d H:i:s', strtotime($end_date)); ?>"
         data-style="<?php echo esc_attr($atts['style']); ?>">
        <div class="countdown-display">
            <span class="countdown-days">00</span>g 
            <span class="countdown-hours">00</span>h 
            <span class="countdown-minutes">00</span>m
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const widget = document.querySelector('[data-end-date]');
        if (!widget) return;
        
        function updateMiniCountdown() {
            const endDate = new Date(widget.getAttribute('data-end-date')).getTime();
            const now = new Date().getTime();
            const distance = endDate - now;
            
            if (distance < 0) {
                widget.innerHTML = '<span class="expired">Scaduto</span>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            widget.querySelector('.countdown-days').textContent = days.toString().padStart(2, '0');
            widget.querySelector('.countdown-hours').textContent = hours.toString().padStart(2, '0');
            widget.querySelector('.countdown-minutes').textContent = minutes.toString().padStart(2, '0');
        }
        
        setInterval(updateMiniCountdown, 60000); // Aggiorna ogni minuto
        updateMiniCountdown();
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('instacontest_countdown', 'instacontest_countdown_shortcode');

// ========================================
// ADMIN UTILITIES
// ========================================

// Aggiungi colonna nell'admin per vedere i vincitori verificati
function instacontest_add_admin_columns($columns) {
    $columns['winner_verified'] = 'Vincitore Verificato';
    $columns['participants'] = 'Partecipanti';
    return $columns;
}
add_filter('manage_contest_posts_columns', 'instacontest_add_admin_columns');

function instacontest_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'winner_verified':
            $is_verified = instacontest_is_winner_verified($post_id);
            if ($is_verified) {
                $winner_data = instacontest_get_winner_data($post_id);
                echo '<span style="color: green;">✅ ' . esc_html($winner_data['username_ig']) . '</span>';
            } else {
                $winner_username = get_field('winner_username', $post_id);
                if (!empty($winner_username)) {
                    echo '<span style="color: orange;">⏳ ' . esc_html($winner_username) . '</span>';
                } else {
                    echo '<span style="color: gray;">-</span>';
                }
            }
            break;
            
        case 'participants':
            $count = instacontest_get_contest_participants_count($post_id);
            echo $count > 0 ? $count : '-';
            break;
    }
}
add_action('manage_contest_posts_custom_column', 'instacontest_admin_column_content', 10, 2);

// ========================================
// CSS PER SHORTCODES
// ========================================

function instacontest_shortcode_styles() {
    if (is_singular('contest') || has_shortcode(get_post()->post_content, 'instacontest_participants') || 
        has_shortcode(get_post()->post_content, 'instacontest_countdown')) {
        ?>
        <style>
        .participants-counter-widget {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 1rem;
            color: white;
        }
        
        .avatar-group {
            display: flex;
            margin-right: -0.5rem;
        }
        
        .participant-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: -0.5rem;
            font-size: 0.875rem;
        }
        
        .instacontest-countdown-widget {
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 0.75rem;
            color: white;
            text-align: center;
            font-weight: 600;
        }
        
        .countdown-display span {
            font-variant-numeric: tabular-nums;
        }
        
        .expired {
            color: #ff6b6b;
            font-weight: bold;
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'instacontest_shortcode_styles');
