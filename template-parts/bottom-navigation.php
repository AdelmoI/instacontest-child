<?php
/**
 * Bottom Navigation Template Part - VERSIONE CORRETTA
 * Barra di navigazione fissa in basso - risolve duplicati
 */

// Ottieni la pagina corrente per evidenziare il tab attivo
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

<!-- Bottom Navigation - ID UNIVOCO per evitare duplicati -->
<nav id="instacontest-bottom-nav" class="fixed bottom-0 w-full bg-white/95 backdrop-blur-lg border-t border-gray-200 z-50 lg:relative lg:mt-8">
    
    <!-- Mobile Navigation -->
    <div class="flex justify-around py-3 px-2 lg:hidden">
        
        <!-- Home/Concorsi -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
           class="flex flex-col items-center group transition-all duration-200 <?php echo ($current_page === 'concorsi') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <div class="relative">
                <i class="fas fa-home text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                <?php if ($current_page === 'concorsi'): ?>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                <?php endif; ?>
            </div>
            <span class="text-xs font-medium">Concorsi</span>
        </a>
        
        <!-- Classifica -->
        <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" 
           class="flex flex-col items-center group transition-all duration-200 <?php echo ($current_page === 'classifica') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <div class="relative">
                <i class="fas fa-trophy text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                <?php if ($current_page === 'classifica'): ?>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                <?php endif; ?>
            </div>
            <span class="text-xs font-medium">Classifica</span>
        </a>
        
        <!-- Regolamento -->
        <a href="<?php echo get_permalink(get_page_by_path('regolamento')); ?>" 
           class="flex flex-col items-center group transition-all duration-200 <?php echo ($current_page === 'regolamento') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <div class="relative">
                <i class="fas fa-file-lines text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                <?php if ($current_page === 'regolamento'): ?>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                <?php endif; ?>
            </div>
            <span class="text-xs font-medium">Regolamento</span>
        </a>
        
        <!-- Profilo -->
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" 
               class="flex flex-col items-center group transition-all duration-200 relative <?php echo ($current_page === 'profilo') ? 'text-blue-500' : 'text-gray-600'; ?>">
                <div class="relative">
                    <?php echo get_avatar(get_current_user_id(), 24, '', '', array('class' => 'w-6 h-6 rounded-full mb-1 border-2 border-gray-300 group-hover:scale-110 transition-transform duration-200')); ?>
                    <?php if ($current_page === 'profilo'): ?>
                        <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                    <?php endif; ?>
                    
                    <!-- Badge punti -->
                    <?php 
                    $user_points = instacontest_get_user_points(get_current_user_id());
                    if ($user_points > 0): 
                    ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center"><?php echo $user_points; ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-xs font-medium">Profilo</span>
            </a>
        <?php else: ?>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" 
               class="flex flex-col items-center group transition-all duration-200 text-gray-600">
                <div class="relative">
                    <i class="fas fa-user text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                </div>
                <span class="text-xs font-medium">Accedi</span>
            </a>
        <?php endif; ?>
        
    </div>

    <!-- Desktop Navigation -->
    <div class="hidden lg:flex lg:justify-center">
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 px-8 py-4">
            <div class="flex space-x-8">
                
                <!-- Home -->
                <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
                   class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl <?php echo ($current_page === 'concorsi') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <i class="fas fa-home text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="text-sm font-medium">Concorsi</span>
                </a>
                
                <!-- Classifica -->
                <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" 
                   class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl <?php echo ($current_page === 'classifica') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <i class="fas fa-trophy text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="text-sm font-medium">Classifica</span>
                </a>
                
                <!-- Regolamento -->
                <a href="<?php echo get_permalink(get_page_by_path('regolamento')); ?>" 
                   class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl <?php echo ($current_page === 'regolamento') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <i class="fas fa-file-lines text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="text-sm font-medium">Regolamento</span>
                </a>
                
                <!-- Profilo -->
                <?php if (is_user_logged_in()): ?>
                    <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" 
                       class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl relative <?php echo ($current_page === 'profilo') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                        <div class="relative">
                            <?php echo get_avatar(get_current_user_id(), 32, '', '', array('class' => 'w-8 h-8 rounded-full mb-2 border-2 border-gray-300 group-hover:scale-110 transition-transform duration-200')); ?>
                            
                            <!-- Badge punti desktop -->
                            <?php 
                            $user_points = instacontest_get_user_points(get_current_user_id());
                            if ($user_points > 0): 
                            ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-2 py-1"><?php echo $user_points; ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="text-sm font-medium">Profilo</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo wp_login_url(get_permalink()); ?>" 
                       class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-user text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                        <span class="text-sm font-medium">Accedi</span>
                    </a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</nav>

<!-- Spacer per evitare che il contenuto sia coperto dalla nav -->
<div class="pb-20 lg:pb-0"></div>
