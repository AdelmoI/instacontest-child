<?php
/**
 * Bottom Navigation Template Part
 * Stile semplice con gradiente Instagram
 */

$current_page = '';
if (is_post_type_archive('contest') || is_home() || is_front_page()) {
    $current_page = 'home';
} elseif (is_page('classifica') || is_page_template('page-classifica.php')) {
    $current_page = 'classifica';
} elseif (is_page('regolamento') || is_page_template('page-regolamento.php')) {
    $current_page = 'regolamento';
} elseif (is_page('profilo') || is_page_template('page-profilo.php') || is_author()) {
    $current_page = 'profilo';
} elseif (is_page('login') || is_page_template('page-login.php')) {
    $current_page = 'profilo'; // Considera login come sezione profilo
} elseif (is_page('register') || is_page_template('page-register.php')) {
    $current_page = 'profilo'; // Considera registrazione come sezione profilo
}
?>

<!-- Bottom Navigation -->
<nav id="bottom-nav" class="fixed bottom-0 left-0 right-0 w-full bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around items-center py-3 px-4 max-w-full mx-auto">
        
        <!-- Home/Concorsi -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" class="flex flex-col items-center">
            <i class="fa-solid fa-home <?php echo ($current_page === 'home') ? 'text-instagram-gradient' : 'text-gray-600'; ?> text-xl mb-1"></i>
            <span class="<?php echo ($current_page === 'home') ? 'text-instagram-gradient' : 'text-gray-600'; ?> text-xs">Home</span>
        </a>
        
        <!-- Classifica -->
        <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" class="flex flex-col items-center">
            <i class="fa-regular fa-chart-bar <?php echo ($current_page === 'classifica') ? 'text-instagram-gradient' : 'text-gray-600'; ?> text-xl mb-1"></i>
            <span class="<?php echo ($current_page === 'classifica') ? 'text-instagram-gradient' : 'text-gray-600'; ?> text-xs">Classifica</span>
        </a>
        
        <!-- Regolamento -->
        <a href="<?php echo get_permalink(get_page_by_path('regolamento')); ?>" class="flex flex-col items-center">
            <i class="fa-regular fa-file-lines <?php echo ($current_page === 'regolamento') ? 'text-instagram-gradient' : 'text-gray-600'; ?> text-xl mb-1"></i>
            <span class="<?php echo ($current_page === 'regolamento') ? 'text-instagram-gradient' : 'text-gray-600'; ?> text-xs">Regolamento</span>
        </a>
        
        <!-- Profilo -->
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo home_url('/profilo'); ?>" class="flex flex-col items-center relative">
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
            <a href="<?php echo home_url('/login'); ?>" class="flex flex-col items-center">
                <i class="fa-regular fa-user text-gray-600 text-xl mb-1"></i>
                <span class="text-gray-600 text-xs">Accedi</span>
            </a>
        <?php endif; ?>
        
    </div>
</nav>

<!-- Spacer per evitare che il contenuto sia coperto dalla nav -->
<div class="pb-20"></div>
