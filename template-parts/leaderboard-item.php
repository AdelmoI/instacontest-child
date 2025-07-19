<?php
/**
 * Template Part: Leaderboard Item
 * Utilizzato per ogni item della classifica
 */

// Variabili disponibili:
// $position, $user, $is_current_user, $user_points, $participations, $wins
?>

<div class="bg-white border border-gray-200 rounded-2xl p-4 <?php echo $is_current_user ? 'ring-2 ring-purple-500 bg-gradient-to-r from-purple-50 to-pink-50' : ''; ?>">
    <div class="flex items-center space-x-4">
        
        <!-- Posizione e Medaglia -->
        <div class="flex-shrink-0 w-12 text-center">
            <?php if ($position <= 3): ?>
                <div class="text-2xl">
                    <?php 
                    $medals = ['🥇', '🥈', '🥉'];
                    echo $medals[$position - 1];
                    ?>
                </div>
            <?php else: ?>
                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                    <span class="text-gray-600 font-bold text-sm"><?php echo $position; ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Avatar con gradiente -->
        <div class="flex-shrink-0">
            <div class="<?php echo $is_current_user ? 'avatar-gradient' : 'p-0.5 bg-gray-200 rounded-full'; ?>">
                <?php echo get_avatar($user->ID, 48, '', '', array('class' => 'w-12 h-12 rounded-full border-2 border-white block')); ?>
            </div>
        </div>
        
        <!-- Info Utente -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2 mb-1">
                <h3 class="text-black font-bold text-sm truncate">
                    @<?php 
                    $instagram_username = instacontest_get_user_instagram($user->ID);
                    echo esc_html($instagram_username ?: $user->user_login); 
                    ?>
                </h3>
                <?php if ($is_current_user): ?>
                    <span class="bg-purple-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">TU</span>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center space-x-3 text-xs text-gray-500">
                <span class="flex items-center space-x-1">
                    <span>🎯</span>
                    <span><?php echo $participations; ?></span>
                </span>
                <?php if ($wins > 0): ?>
                    <span class="flex items-center space-x-1 text-yellow-600">
                        <span>🏆</span>
                        <span><?php echo $wins; ?></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Punteggio -->
        <div class="flex-shrink-0 text-right">
            <div class="text-lg font-bold <?php echo $is_current_user ? 'text-purple-600' : 'text-gray-800'; ?>">
                <?php echo number_format($user_points); ?>
            </div>
            <div class="text-xs text-gray-500">punti</div>
        </div>
        
    </div>
</div>
