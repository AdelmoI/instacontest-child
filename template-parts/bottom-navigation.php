<?php
/**
 * Bottom Navigation Template Part - Con Tailwind CSS
 * Design moderno e responsive con classi Tailwind
 */

$current_page = '';
if (is_post_type_archive('contest') || is_home() || is_front_page()) {
    $current_page = 'home';
} elseif (is_singular('contest')) {
    $current_page = 'concorsi';
} elseif (is_page('classifica')) {
    $current_page = 'premi';
} elseif (is_page('profilo') || is_author()) {
    $current_page = 'profilo';
}

// Ottieni avatar utente se loggato
$user_avatar = '';
$user_points = 0;
if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $user_avatar = get_avatar_url($current_user->ID, array('size' => 24));
    $user_points = instacontest_get_user_points($current_user->ID);
}
?>

<!-- Bottom Navigation con Tailwind CSS -->
<nav id="bottom-nav" class="fixed bottom-0 w-full bg-white/95 backdrop-blur-lg border-t border-gray-200 z-50 lg:relative lg:mt-8">
    <!-- Mobile Navigation -->
    <div class="flex justify-around py-3 px-2 lg:hidden">
        
        <!-- Home -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
           class="flex flex-col items-center group transition-all duration-200 <?php echo ($current_page === 'home') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <div class="relative">
                <i class="fas fa-home text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                <?php if ($current_page === 'home'): ?>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                <?php endif; ?>
            </div>
            <span class="text-xs font-medium">Home</span>
        </a>
        
        <!-- Concorsi -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
           class="flex flex-col items-center group transition-all duration-200 <?php echo ($current_page === 'concorsi') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <div class="relative">
                <i class="fas fa-trophy text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                <?php if ($current_page === 'concorsi'): ?>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                <?php endif; ?>
            </div>
            <span class="text-xs font-medium">Concorsi</span>
        </a>
        
        <!-- Premi / Classifica -->
        <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" 
           class="flex flex-col items-center group transition-all duration-200 <?php echo ($current_page === 'premi') ? 'text-blue-500' : 'text-gray-600'; ?>">
            <div class="relative">
                <i class="fas fa-gift text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                <?php if ($current_page === 'premi'): ?>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                <?php endif; ?>
            </div>
            <span class="text-xs font-medium">Premi</span>
        </a>
        
        <!-- Profilo -->
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" 
               class="flex flex-col items-center group transition-all duration-200 relative <?php echo ($current_page === 'profilo') ? 'text-blue-500' : 'text-gray-600'; ?>">
                <div class="relative">
                    <?php if ($user_avatar): ?>
                        <img src="<?php echo esc_url($user_avatar); ?>" 
                             alt="Profilo" 
                             class="w-6 h-6 rounded-full mb-1 border-2 <?php echo ($current_page === 'profilo') ? 'border-blue-500' : 'border-gray-300'; ?> group-hover:scale-110 transition-transform duration-200">
                    <?php else: ?>
                        <i class="fas fa-user text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
                    <?php endif; ?>
                    
                    <!-- Badge punti -->
                    <?php if ($user_points > 0): ?>
                        <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                            <?php echo $user_points > 99 ? '99+' : $user_points; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($current_page === 'profilo'): ?>
                        <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                    <?php endif; ?>
                </div>
                <span class="text-xs font-medium">Profilo</span>
            </a>
        <?php else: ?>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" 
               class="flex flex-col items-center group transition-all duration-200 text-gray-600">
                <div class="relative">
                    <i class="fas fa-sign-in-alt text-xl mb-1 group-hover:scale-110 transition-transform duration-200"></i>
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
                   class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl <?php echo ($current_page === 'home') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <i class="fas fa-home text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="text-sm font-medium">Home</span>
                </a>
                
                <!-- Concorsi -->
                <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
                   class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl <?php echo ($current_page === 'concorsi') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <i class="fas fa-trophy text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="text-sm font-medium">Concorsi</span>
                </a>
                
                <!-- Premi -->
                <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" 
                   class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl <?php echo ($current_page === 'premi') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                    <i class="fas fa-gift text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="text-sm font-medium">Premi</span>
                </a>
                
                <!-- Profilo -->
                <?php if (is_user_logged_in()): ?>
                    <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" 
                       class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl relative <?php echo ($current_page === 'profilo') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'; ?>">
                        <div class="relative">
                            <?php if ($user_avatar): ?>
                                <img src="<?php echo esc_url($user_avatar); ?>" 
                                     alt="Profilo" 
                                     class="w-8 h-8 rounded-full mb-2 border-2 <?php echo ($current_page === 'profilo') ? 'border-blue-500' : 'border-gray-300'; ?> group-hover:scale-110 transition-transform duration-200">
                            <?php else: ?>
                                <i class="fas fa-user text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                            <?php endif; ?>
                            
                            <!-- Badge punti desktop -->
                            <?php if ($user_points > 0): ?>
                                <div class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">
                                    <?php echo $user_points > 99 ? '99+' : $user_points; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <span class="text-sm font-medium">Profilo</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo wp_login_url(get_permalink()); ?>" 
                       class="flex flex-col items-center group transition-all duration-300 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-sign-in-alt text-2xl mb-2 group-hover:scale-110 transition-transform duration-200"></i>
                        <span class="text-sm font-medium">Accedi</span>
                    </a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</nav>

<!-- Spacer per mobile -->
<div class="h-20 lg:hidden"></div>

<!-- JavaScript per animazioni aggiuntive -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animazione di caricamento della bottom nav
    const bottomNav = document.getElementById('bottom-nav');
    if (bottomNav) {
        bottomNav.style.transform = 'translateY(100%)';
        bottomNav.style.opacity = '0';
        
        setTimeout(() => {
            bottomNav.style.transition = 'all 0.4s ease-out';
            bottomNav.style.transform = 'translateY(0)';
            bottomNav.style.opacity = '1';
        }, 100);
    }
    
    // Animazione touch per mobile
    const navItems = document.querySelectorAll('#bottom-nav a');
    navItems.forEach(item => {
        item.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        item.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });
});



// Force fix bottom navigation layout
document.addEventListener('DOMContentLoaded', function() {
    const bottomNav = document.getElementById('bottom-nav');
    const navContainer = bottomNav.querySelector('div');
    const navLinks = bottomNav.querySelectorAll('a');
    
    // Force styles via JavaScript as backup
    if (bottomNav) {
        bottomNav.style.cssText = `
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 999999 !important;
            background: rgba(255, 255, 255, 0.95) !important;
            border-top: 1px solid #e5e7eb !important;
        `;
    }
    
    if (navContainer) {
        navContainer.style.cssText = `
            display: flex !important;
            justify-content: space-around !important;
            align-items: center !important;
            padding: 12px 8px 8px 8px !important;
        `;
    }
    
    navLinks.forEach(link => {
        link.style.cssText = `
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-decoration: none !important;
            width: 25% !important;
            text-align: center !important;
        `;
    });
});
</script>
