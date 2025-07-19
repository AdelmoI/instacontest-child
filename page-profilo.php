<?php
/**
 * Template Name: Profilo
 * Pagina profilo utente con stile identico al login
 */

get_header(); 
// DEBUG: Verifica stato login
error_log('DEBUG PROFILO - User logged in: ' . (is_user_logged_in() ? 'YES' : 'NO'));
if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    error_log('DEBUG PROFILO - User ID: ' . $current_user->ID);
    error_log('DEBUG PROFILO - Display name: ' . $current_user->display_name);
} else {
    error_log('DEBUG PROFILO - Redirecting to login');
}
// Redirect se non loggato
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_points = instacontest_get_user_points($user_id);
$user_position = instacontest_get_user_position($user_id);
$user_participations = instacontest_get_user_participations($user_id);
$user_wins = instacontest_get_user_wins($user_id);
$instagram_username = instacontest_get_user_instagram($user_id);
?>

<body class="bg-gray-50">

    <!-- Contenuto Profilo -->
    <section class="px-4 py-6 bg-gray-50 min-h-screen">
        <div class="max-w-md mx-auto md:max-w-lg lg:max-w-xl">
            
            <!-- Header Sezione -->
            <div class="text-center mb-6 md:mb-8">
                <img src="https://www.instacontest.it/wp-content/uploads/2025/06/Progetto-senza-titolo-52.png" 
                     alt="InstaContest" 
                     class="w-18 h-18 md:w-24 md:h-24 object-contain mx-auto mb-4">
                <h1 class="text-black font-bold text-2xl md:text-3xl mb-2">Il tuo Profilo</h1>
                <p class="text-gray-500 text-lg md:text-xl">Ciao <?php echo esc_html($current_user->first_name ?: $current_user->display_name); ?>!</p>
            </div>

            <?php
            // Gestione upload avatar
            $upload_success = false;
            $upload_error = '';

            if (isset($_POST['upload_avatar']) && isset($_FILES['avatar_file'])) {
                if (!wp_verify_nonce($_POST['avatar_nonce'], 'upload_avatar')) {
                    $upload_error = 'Errore di sicurezza';
                } else {
                    $file = $_FILES['avatar_file'];
                    
                    // Validazioni
                    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
                    $max_size = 2 * 1024 * 1024; // 2MB
                    
                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        $upload_error = 'Errore durante l\'upload';
                    } elseif (!in_array($file['type'], $allowed_types)) {
                        $upload_error = 'Solo file JPG e PNG sono permessi';
                    } elseif ($file['size'] > $max_size) {
                        $upload_error = 'File troppo grande (max 2MB)';
                    } else {
                        // Upload del file
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        
                        $upload = wp_handle_upload($file, array('test_form' => false));
                        
                        if (!isset($upload['error'])) {
                            // Crea attachment
                            $attachment = array(
                                'post_mime_type' => $file['type'],
                                'post_title' => 'Avatar - ' . $current_user->display_name,
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );
                            
                            $attachment_id = wp_insert_attachment($attachment, $upload['file']);
                            
                            if ($attachment_id) {
                                wp_generate_attachment_metadata($attachment_id, $upload['file']);
                                update_user_meta($user_id, 'custom_avatar', $attachment_id);
                                $upload_success = true;
                            }
                        } else {
                            $upload_error = $upload['error'];
                        }
                    }
                }
            }
            ?>

            <!-- Messaggi upload -->
            <?php if ($upload_success): ?>
                <div class="bg-white border border-green-200 rounded-2xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="text-black font-bold">Avatar aggiornato!</h3>
                            <p class="text-gray-500 text-sm">La tua immagine profilo è stata caricata</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($upload_error): ?>
                <div class="bg-white border border-red-200 rounded-2xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fa-solid fa-exclamation-triangle text-red-500 mr-3 text-xl"></i>
                        <div>
                            <h3 class="text-black font-bold">Errore upload</h3>
                            <p class="text-gray-500 text-sm"><?php echo esc_html($upload_error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Informazioni Utente -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="relative">
                        <div class="p-1 avatar-gradient rounded-full">
                            <?php 
                            // Usa avatar personalizzato se esiste
                            $custom_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
                            if ($custom_avatar_id) {
                                $avatar_url = wp_get_attachment_image_url($custom_avatar_id, 'thumbnail');
                                echo '<img src="' . esc_url($avatar_url) . '" alt="Avatar" class="w-16 h-16 rounded-full border-2 border-white object-cover">';
                            } else {
                                echo get_avatar($user_id, 64, '', '', array('class' => 'w-16 h-16 rounded-full border-2 border-white'));
                            }
                            ?>
                        </div>
                        
                        <!-- Pulsante upload avatar -->
                        <button onclick="openAvatarUpload()" 
                                class="absolute -bottom-0.5 -right-0.5 w-5 h-5 bg-blue-500 text-white rounded-full flex items-center justify-center hover:bg-blue-600 transition cursor-pointer border-2 border-white shadow-lg avatar-upload-btn">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                        
                        <!-- Form upload nascosto -->
                        <form method="post" enctype="multipart/form-data" id="avatar-form" style="display: none;">
                            <?php wp_nonce_field('upload_avatar', 'avatar_nonce'); ?>
                            <input type="file" 
                                   id="avatar-upload" 
                                   name="avatar_file" 
                                   accept="image/jpeg,image/jpg,image/png"
                                   onchange="document.getElementById('avatar-form').submit();">
                            <input type="hidden" name="upload_avatar" value="1">
                        </form>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-black font-bold text-lg"><?php echo esc_html($current_user->display_name); ?></h2>
                        <p class="text-gray-500 text-sm"><?php echo esc_html($current_user->user_email); ?></p>
                        <?php if ($instagram_username): ?>
                            <p class="text-purple-600 text-sm font-medium">@<?php echo esc_html($instagram_username); ?></p>
                        <?php else: ?>
                            <p class="text-red-500 text-sm">Username Instagram mancante</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Statistiche -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-black"><?php echo $user_points; ?></div>
                        <div class="text-sm text-gray-500">Punti</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-black">#<?php echo $user_position; ?></div>
                        <div class="text-sm text-gray-500">Posizione</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-black"><?php echo $user_participations; ?></div>
                        <div class="text-sm text-gray-500">Partecipazioni</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-black"><?php echo $user_wins; ?></div>
                        <div class="text-sm text-gray-500">Vittorie</div>
                    </div>
                </div>
            </div>

            <!-- Contest Partecipati -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <h3 class="text-black font-bold text-lg mb-4">I tuoi Contest</h3>

                <!-- AGGIUNGI QUESTO BLOCCO QUI -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-yellow-800 text-sm">
                        <span class="font-semibold">⚠️ Ricorda:</span> Segui tutte le regole del contest per validare la partecipazione.
                    </p>
                </div>
                <!-- FINE BLOCCO DA AGGIUNGERE -->
                
                <?php
                // Ottieni contest a cui l'utente ha partecipato
                global $wpdb;
                $participated_contests = $wpdb->get_results($wpdb->prepare("
                    SELECT meta_key 
                    FROM {$wpdb->usermeta} 
                    WHERE user_id = %d 
                    AND meta_key LIKE 'participated_contest_%'
                ", $user_id));

                if ($participated_contests): ?>
                    <div class="space-y-3">
                        <?php 
                        foreach ($participated_contests as $participation):
                            $contest_id = str_replace('participated_contest_', '', $participation->meta_key);
                            $contest = get_post($contest_id);
                            if ($contest):
                                $prize_name = get_field('prize_name', $contest_id);
                                $prize_image = get_field('prize_image', $contest_id);
                                $status = instacontest_get_contest_status($contest_id);
                                ?>
                                
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                                    <?php if ($prize_image): ?>
                                        <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>"
                                             class="w-12 h-12 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-gift text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex-1">
                                        <h4 class="text-black font-medium text-sm"><?php echo esc_html($prize_name); ?></h4>
                                        <p class="text-gray-500 text-xs"><?php echo get_the_date('d/m/Y', $contest_id); ?></p>
                                    </div>
                                    
                                    <div class="text-right">
                                        <?php if ($status === 'active'): ?>
                                            <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs font-medium">Attivo</span>
                                        <?php elseif ($status === 'completed'): ?>
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">Terminato</span>
                                        <?php else: ?>
                                            <span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs font-medium">In corso</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                            <?php endif;
                        endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fa-solid fa-trophy text-gray-300 text-4xl mb-4"></i>
                        <h4 class="text-gray-500 font-medium mb-2">Nessuna partecipazione</h4>
                        <p class="text-gray-400 text-sm mb-4">Non hai ancora partecipato a nessun contest</p>
                        <a href="<?php echo home_url(); ?>" class="text-blue-500 hover:text-blue-600 font-medium text-sm">
                            Scopri i contest aperti
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Impostazioni Account -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <h3 class="text-black font-bold text-lg mb-4">Impostazioni</h3>
                
                <div class="space-y-3">
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="flex items-center justify-between py-3 px-4 bg-red-50 rounded-xl hover:bg-red-100 transition">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-sign-out-alt text-red-600"></i>
                            <span class="text-red-600 font-medium">Esci</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-red-400"></i>
                    </a>
                </div>
            </div>

            <!-- Link ai Contest -->
            <div class="text-center">
                <a href="<?php echo home_url(); ?>" 
                   class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-600 font-medium">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Torna ai Contest</span>
                </a>
            </div>

        </div>
    </section>

</body>

<!-- JavaScript per upload avatar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarUpload = document.getElementById('avatar-upload');
    const avatarForm = document.getElementById('avatar-form');
    const uploadBtn = document.querySelector('.avatar-upload-btn');
    
    if (avatarUpload) {
        avatarUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validazioni client-side
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Solo file JPG e PNG sono permessi');
                    e.target.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    alert('File troppo grande (max 2MB)');
                    e.target.value = '';
                    return;
                }
                
                // Mostra loading
                if (uploadBtn) {
                    uploadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';
                    uploadBtn.style.pointerEvents = 'none';
                }
                
                // Submit form
                avatarForm.submit();
            }
        });
    }
});

function openAvatarUpload() {
    document.getElementById('avatar-upload').click();
}
</script>

<?php get_footer(); ?>
