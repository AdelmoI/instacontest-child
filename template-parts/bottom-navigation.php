<?php
/**
 * Bottom Navigation Template Part
 * Stile semplice con gradiente Instagram
 */

$current_page = '';
if (is_post_type_archive('contest') || is_home() || is_front_page()) {
    $current_page = 'home';
} elseif (is_page('classifica')) {
    $current_page = 'classifica';
} elseif (is_page('regolamento')) {
    $current_page = 'regolamento';
} elseif (is_page('profilo') || is_author()) {
    $current_page = 'profilo';
} elseif (is_page('login')) {
    $current_page = 'login';
} elseif (is_page('register')) {
    $current_page = 'register';
}
?>

<!-- Profilo / Login -->
        <?php if (is_user_logged_in()): ?>
            <!-- UTENTE LOGGATO - Vai al profilo -->
            <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" class="flex flex-col items-center relative">
                <div class="relative">
                    <?php echo get_avatar(get_current_user_id(), 24, '', '', array('class' => 'w-6 h-6 rounded-full mb-1')); ?>
                    
                    <!-- Badge punti -->
                    <?php 
                    $user_points = instacontest_get_user_points(get_current_user_id());
                    if ($user_points > 0): 
                    ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-bold text-xs"><?php echo $user_points > 99 ? '99+' : $user_points; ?></span>
                    <?php endif; ?>
                </div>
                <span class="<?php echo ($current_page === 'profilo') ? 'text-instagram-gradient' : 'text-gray-600'; ?> text-xs">Profilo</span>
            </a>
        <?php else: ?>
            <!-- UTENTE NON LOGGATO - Link intelligente -->
            <?php 
            // Determina se mostrare Login o Registrati
            $show_register = false;
            
            // Se siamo sulla pagina login, mostra "Registrati"
            if (is_page('login')) {
                $show_register = true;
                $link_url = get_permalink(get_page_by_path('register'));
                $link_text = 'Registrati';
                $icon_class = 'fa-user-plus';
            } 
            // Se siamo sulla pagina registrazione, mostra "Accedi"
            elseif (is_page('register')) {
                $link_url = get_permalink(get_page_by_path('login'));
                $link_text = 'Accedi';
                $icon_class = 'fa-sign-in-alt';
            }
            // Altrimenti, link predefinito al login
            else {
                $link_url = get_permalink(get_page_by_path('login'));
                $link_text = 'Accedi';
                $icon_class = 'fa-sign-in-alt';
            }
            ?>
            <a href="<?php echo $link_url; ?>" class="flex flex-col items-center">
                <i class="fa-solid <?php echo $icon_class; ?> text-gray-600 text-xl mb-1"></i>
                <span class="text-gray-600 text-xs"><?php echo $link_text; ?></span>
            </a>
        <?php endif; ?>
