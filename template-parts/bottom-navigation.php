<?php
/**
 * Bottom Navigation Template Part
 * Barra di navigazione fissa in basso
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

<nav class="bottom-navigation" id="bottom-nav">
    <div class="nav-container">
        
        <!-- Concorsi -->
        <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
           class="nav-item <?php echo ($current_page === 'concorsi') ? 'active' : ''; ?>">
            <div class="nav-icon">
                <?php if ($current_page === 'concorsi'): ?>
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                <?php endif; ?>
            </div>
            <span class="nav-label">Concorsi</span>
        </a>
        
        <!-- Classifica -->
        <a href="<?php echo get_permalink(get_page_by_path('classifica')); ?>" 
           class="nav-item <?php echo ($current_page === 'classifica') ? 'active' : ''; ?>">
            <div class="nav-icon">
                <?php if ($current_page === 'classifica'): ?>
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 13v6h4v-6H7zM9.5 9.5v2h-2v-2h2zM13 7v10h4V7h-4zM15.5 9.5v2h-2v-2h2zM4 17v2h16v-2H4z"/>
                    </svg>
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18"/>
                        <path d="M18 17V9"/>
                        <path d="M13 17V5"/>
                        <path d="M8 17v-3"/>
                    </svg>
                <?php endif; ?>
            </div>
            <span class="nav-label">Classifica</span>
        </a>
        
        <!-- Regolamento -->
        <a href="<?php echo get_permalink(get_page_by_path('regolamento')); ?>" 
           class="nav-item <?php echo ($current_page === 'regolamento') ? 'active' : ''; ?>">
            <div class="nav-icon">
                <?php if ($current_page === 'regolamento'): ?>
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M16 13H8"/>
                        <path d="M16 17H8"/>
                        <path d="M10 9H8"/>
                    </svg>
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M16 13H8"/>
                        <path d="M16 17H8"/>
                        <path d="M10 9H8"/>
                    </svg>
                <?php endif; ?>
            </div>
            <span class="nav-label">Regolamento</span>
        </a>
        
        <!-- Profilo -->
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo get_permalink(get_page_by_path('profilo')); ?>" 
               class="nav-item <?php echo ($current_page === 'profilo') ? 'active' : ''; ?>">
                <div class="nav-icon">
                    <?php if ($current_page === 'profilo'): ?>
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <span class="nav-label">Profilo</span>
                
                <!-- Badge punti -->
                <?php 
                $user_points = instacontest_get_user_points(get_current_user_id());
                if ($user_points > 0): 
                ?>
                    <span class="nav-badge"><?php echo $user_points; ?></span>
                <?php endif; ?>
            </a>
        <?php else: ?>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="nav-item">
                <div class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10,17 15,12 10,7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>
                <span class="nav-label">Accedi</span>
            </a>
        <?php endif; ?>
        
    </div>
</nav>

<!-- Spacer per evitare che il contenuto sia coperto dalla nav -->
<div class="bottom-nav-spacer"></div>