<?php
/**
 * Bottom Navigation Template Part
 * Sempre fixed bottom su tutti i dispositivi
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
}
?>

<!-- Bottom Navigation -->
<nav id="bottom-nav" class="fixed bottom-0 w-full bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around py-3">
        
        <!-- Home -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" class="flex flex-col items-center">
            <i class="fa-regular fa-house <?php echo ($current_page === 'home') ? 'text-blue-500' : 'text-gray-600'; ?> text-xl mb-1"></i>
            <span class="<?php echo ($current_page === 'home') ? 'text-blue-500' : 'text-gray-600'; ?> text-xs">Home</span>
        </a>
        
        <!-- Concorsi -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" class="flex flex-col items-center">
            <i class="fa-regular fa-trophy <?php echo ($current_page === 'concorsi') ? 'text-blue-500' : 'text-gray-600'; ?> text-xl mb-1"></i>
            <span class="<?php echo ($current_page === 'concorsi') ? 'text-blue-500' : 'text-gray-600'; ?> text-xs">Concorsi</span>
        </a>
        
        <!-- Premi/Classifica -->
        <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" class="flex flex-col items-center">
            <i class="fa-regular fa-file-lines <?php echo ($current_page === 'classifica') ? 'text-blue-500' : 'text-gray-600'; ?> text-xl mb-1"></i>
            <span class="<?php echo ($current_page === 'classifica') ? 'text-blue-500' : 'text-gray-600'; ?> text-xs">Premi</span>
        </a>
        
        <!-- Profilo -->
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" class="flex flex-col items-center relative">
                <div class="relative">
                    <?php echo get_avatar(get_current_user_id(), 24, '', '', array('class' => 'w-6 h-6 rounded-full mb-1')); ?>
                    
                    <!-- Badge punti -->
                    <?php 
                    $user_points = instacontest_get_user_points(get_current_user_id());
                    if ($user_points > 0): 
                    ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs font-bold px-1 min-w-4 h-4 flex items-center justify-center"><?php echo $user_points > 99 ? '99+' : $user_points; ?></span>
                    <?php endif; ?>
                </div>
                <span class="<?php echo ($current_page === 'profilo') ? 'text-blue-500' : 'text-gray-600'; ?> text-xs">Profilo</span>
            </a>
        <?php else: ?>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="flex flex-col items-center">
                <i class="fa-regular fa-user text-gray-600 text-xl mb-1"></i>
                <span class="text-gray-600 text-xs">Accedi</span>
            </a>
        <?php endif; ?>
        
    </div>
</nav>

<!-- Spacer per il contenuto -->
<div class="pb-20"></div>
