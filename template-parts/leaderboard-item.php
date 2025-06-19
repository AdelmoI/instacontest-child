<?php
/**
 * Template Part: Leaderboard Item
 * Utilizzato per ogni item della classifica
 */

// Variabili disponibili:
// $position, $user, $is_current_user, $user_points, $participations, $wins
?>

<div class="leaderboard-item <?php echo $is_current_user ? 'current-user' : ''; ?>">
    <div class="rank">
        <?php if ($position <= 3): ?>
            <span class="medal">
                <?php 
                $medals = ['🥇', '🥈', '🥉'];
                echo $medals[$position - 1];
                ?>
            </span>
        <?php else: ?>
            <span class="rank-number"><?php echo $position; ?></span>
        <?php endif; ?>
    </div>
    
    <div class="user-avatar">
        <?php echo get_avatar($user->ID, 50); ?>
    </div>
    
    <div class="user-info">
        <h3 class="username">
            @<?php echo esc_html($user->user_login); ?>
            <?php if ($is_current_user): ?>
                <span class="you-badge">Tu</span>
            <?php endif; ?>
        </h3>
        <div class="user-stats">
            <span class="stat">
                🎯 <?php echo $participations; ?> partecipazioni
            </span>
            <?php if ($wins > 0): ?>
                <span class="stat wins">
                    🏆 <?php echo $wins; ?> vittorie
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="user-points">
        <span class="points-number"><?php echo $user_points; ?></span>
        <span class="points-label">punti</span>
    </div>
    
    <div class="user-trend">
        <?php 
        // Trend simulato - implementare logica reale se necessario
        $trend = rand(-1, 1);
        if ($trend > 0): ?>
            <span class="trend up">↗️</span>
        <?php elseif ($trend < 0): ?>
            <span class="trend down">↘️</span>
        <?php else: ?>
            <span class="trend stable">→</span>
        <?php endif; ?>
    </div>
</div>