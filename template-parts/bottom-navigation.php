<?php
/**
 * Bottom Navigation Template Part
 * Barra di navigazione responsive - mobile fixed, desktop centered
 */

$current_page = '';
if (is_post_type_archive('contest') || is_home() || is_front_page()) {
    $current_page = 'concorsi';
} elseif (is_page('classifica')) {
    $current_page = 'classifica';
} elseif (is_page('regolamento')) {
    $current_page = 'regolamento';
} elseif (is_page('profilo') || is_author()) {
    $current_page = 'profilo';
}
?>

<!-- Bottom Navigation - Responsive con Tailwind -->
<nav id="bottom-nav" class="fixed bottom-0 w-full bg-white/95 backdrop-blur-lg border-t border-gray-200 z-50 lg:relative lg:bottom-auto lg:mt-8 lg:bg-transparent lg:border-t-0">
    
    <!-- Mobile e Desktop Container -->
    <div class="flex justify-around py-3 px-2 lg:justify-center">
        
        <!-- Desktop Wrapper (nascosto su mobile) -->
        <div class="hidden lg:flex lg:bg-white/90 lg:backdrop-blur-lg lg:rounded-2xl lg:shadow-xl lg:border lg:border-gray-200/50 lg:px-8 lg:py-4 lg:space-x-8">
            
            <!-- Desktop Items -->
            <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
               class="flex flex-col items-center px-4 py-2 rounded-xl transition-all duration-300 <?php echo ($current_page === 'concorsi') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-trophy text-2xl mb-2"></i>
                <span class="text-sm font-medium">Concorsi</span>
            </a>
            
            <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" 
               class="flex flex-col items-center px-4 py-2 rounded-xl transition-all duration-300 <?php echo ($current_page === 'classifica') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-chart-bar text-2xl mb-2"></i>
                <span class="text-sm font-medium">Classifica</span>
            </a>
            
            <a href="<?php echo get_permalink(get_page_by_path('regolamento')); ?>" 
               class="flex flex-col items-center px-4 py-2 rounded-xl transition-all duration-300 <?php echo ($current_page === 'regolamento') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                <i class="fas fa-file-lines text-2xl mb-2"></i>
                <span class="text-sm font-medium">Regolamento</span>
            </a>
            
            <?php if (is_user_logged_in()): ?>
                <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" 
                   class="flex flex-col items-center px-4 py-2 rounded-xl transition-all duration-300 relative <?php echo ($current_page === 'profilo') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <div class="relative">
                        <?php echo get_avatar(get_current_user_id(), 32, '', '', array('class' => 'w-8 h-8 rounded-full mb-2 border-2 border-gray-300')); ?>
                        <?php 
                        $user_points = instacontest_get_user_points(get_current_user_id());
                        if ($user_points > 0): 
                        ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold"><?php echo $user_points > 99 ? '99+' : $user_points; ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="text-sm font-medium">Profilo</span>
                </a>
            <?php else: ?>
                <a href="<?php echo wp_login_url(get_permalink()); ?>" 
                   class="flex flex-col items-center px-4 py-2 rounded-xl transition-all duration-300 text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-sign-in-alt text-2xl mb-2"></i>
                    <span class="text-sm font-medium">Accedi</span>
                </a>
            <?php endif; ?>
            
        </div>
        
        <!-- Mobile Items (visibili solo su mobile) -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
           class="flex flex-col items-center transition-all duration-200 lg:hidden <?php echo ($current_page === 'concorsi') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <i class="fas fa-trophy text-xl mb-1"></i>
            <span class="text-xs">Concorsi</span>
        </a>
        
        <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" 
           class="flex flex-col items-center transition-all duration-200 lg:hidden <?php echo ($current_page === 'classifica') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <i class="fas fa-chart-bar text-xl mb-1"></i>
            <span class="text-xs">Classifica</span>
        </a>
        
        <a href="<?php echo get_permalink(get_page_by_path('regolamento')); ?>" 
           class="flex flex-col items-center transition-all duration-200 lg:hidden <?php echo ($current_page === 'regolamento') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <i class="fas fa-file-lines text-xl mb-1"></i>
            <span class="text-xs">Regolamento</span>
        </a>
        
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" 
               class="flex flex-col items-center relative transition-all duration-200 lg:hidden <?php echo ($current_page === 'profilo') ? 'text-blue-500' : 'text-gray-600'; ?>">
                <div class="relative">
                    <?php echo get_avatar(get_current_user_id(), 24, '', '', array('class' => 'w-6 h-6 rounded-full mb-1')); ?>
                    <?php 
                    $user_points = instacontest_get_user_points(get_current_user_id());
                    if ($user_points > 0): 
                    ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-bold"><?php echo $user_points > 99 ? '99+' : $user_points; ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-xs">Profilo</span>
            </a>
        <?php else: ?>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" 
               class="flex flex-col items-center transition-all duration-200 lg:hidden text-gray-600">
                <i class="fas fa-user text-xl mb-1"></i>
                <span class="text-xs">Accedi</span>
            </a>
        <?php endif; ?>
        
    </div>
</nav>

<!-- Spacer solo per mobile -->
<div class="h-20 lg:h-0"></div>
