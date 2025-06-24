<?php
/**
 * Archive Contest Template - Homepage
 * Mostra contest attivi e passati con immagini in evidenza
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main instacontest-homepage">
        
        <!-- Header con logo e stats utente -->
        <div class="homepage-header">
            <div class="container">
                <div class="header-content">
                    <h1 class="site-title">
                        <span class="logo-icon">🎯</span>
                        InstaContest
                    </h1>
                    
                    <?php if (is_user_logged_in()): ?>
                        <?php 
                        $user = wp_get_current_user();
                        $points = instacontest_get_user_points(get_current_user_id());
                        ?>
                        <div class="user-stats">
                            <div class="user-greeting">
                                <span class="greeting-text">@<?php echo esc_html($user->user_login); ?></span>
                                <span class="user-points"><?php echo $points; ?> punti</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="<?php echo wp_login_url(); ?>" class="btn-login">Accedi</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contest Attivi -->
        <section class="contests-section active-contests">
            <div class="container">
                <h2 class="section-title">
                    <span class="title-icon">🔥</span>
                    Concorsi aperti
                </h2>
                
                <div class="contests-grid">
                    <?php 
                    $active_contests = instacontest_get_active_contests();
                    if ($active_contests): 
                        foreach ($active_contests as $contest):
                            setup_postdata($contest);
                            $contest_id = $contest->ID;
                            $end_date = get_field('contest_end_date', $contest_id);
                            $prize_name = get_field('prize_name', $contest_id);
                            $prize_value = get_field('prize_value', $contest_id);
                            $prize_image = get_field('prize_image', $contest_id);
                            $participation_points = get_field('participation_points', $contest_id) ?: 5;
                            ?>
                            
                            <div class="contest-card active">
                                <div class="contest-image">
                                    <!-- Immagine in evidenza principale -->
                                    <?php if (has_post_thumbnail($contest_id)): ?>
                                        <?php echo get_the_post_thumbnail($contest_id, 'medium', array('alt' => get_the_title($contest_id))); ?>
                                    <?php else: ?>
                                        <div class="placeholder-image">🎯</div>
                                    <?php endif; ?>
                                    
                                    <div class="contest-status active-badge">ATTIVO</div>
                                    
                                    <!-- Piccola immagine premio in overlay -->
                                    <?php if ($prize_image): ?>
                                        <div class="prize-mini-image">
                                            <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                                 alt="<?php echo esc_attr($prize_name); ?>"
                                                 class="w-12 h-12 object-cover rounded-lg border-2 border-white shadow-sm">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="contest-info">
                                    <h3 class="contest-title"><?php echo esc_html($prize_name); ?></h3>
                                    <p class="contest-meta">
                                        <span class="prize-value">Valore: €<?php echo number_format($prize_value, 0, ',', '.'); ?></span>
                                        <span class="contest-points">+<?php echo $participation_points; ?> punti</span>
                                    </p>
                                    
                                    <div class="contest-countdown-mini" 
                                         data-end-date="<?php echo date('Y-m-d H:i:s', strtotime($end_date)); ?>">
                                        <span class="countdown-label">Termina tra:</span>
                                        <span class="countdown-timer-mini"></span>
                                    </div>
                                    
                                    <a href="<?php echo get_permalink($contest_id); ?>" class="btn-contest-action">
                                        Partecipa
                                    </a>
                                </div>
                            </div>
                            
                        <?php endforeach; 
                        wp_reset_postdata();
                    else: ?>
                        <div class="no-contests">
                            <p>🔍 Nessun concorso attivo al momento</p>
                            <p class="no-contests-sub">Torna presto per nuove opportunità!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Contest Passati -->
        <section class="contests-section past-contests">
            <div class="container">
                <h2 class="section-title">
                    <span class="title-icon">🏆</span>
                    Contest terminati
                </h2>
                
                <div class="contests-list">
                    <?php 
                    $ended_contests = instacontest_get_ended_contests();
                    if ($ended_contests): 
                        $count = 0;
                        foreach ($ended_contests as $contest):
                            if ($count >= 5) break; // Mostra max 5 contest passati
                            setup_postdata($contest);
                            $contest_id = $contest->ID;
                            $prize_name = get_field('prize_name', $contest_id);
                            $prize_image = get_field('prize_image', $contest_id);
                            $has_winner = instacontest_has_winner($contest_id);
                            $status = instacontest_get_contest_status($contest_id);
                            ?>
                            
                            <div class="contest-item past">
                                <div class="contest-thumb">
                                    <!-- Immagine in evidenza per contest passati -->
                                    <?php if (has_post_thumbnail($contest_id)): ?>
                                        <?php echo get_the_post_thumbnail($contest_id, 'thumbnail', array('alt' => get_the_title($contest_id))); ?>
                                    <?php else: ?>
                                        <div class="placeholder-thumb">🏆</div>
                                    <?php endif; ?>
                                    
                                    <!-- Piccola immagine premio in overlay -->
                                    <?php if ($prize_image): ?>
                                        <div class="prize-mini-thumb">
                                            <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                                 alt="<?php echo esc_attr($prize_name); ?>"
                                                 class="w-6 h-6 object-cover rounded border border-white">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="contest-details">
                                    <h4 class="contest-name"><?php echo esc_html($prize_name); ?></h4>
                                    <p class="contest-date">
                                        <?php echo get_the_date('d/m/Y'); ?>
                                    </p>
                                </div>
                                
                                <div class="contest-action">
                                    <?php if ($status === 'completed'): ?>
                                        <a href="<?php echo get_permalink($contest_id); ?>" class="btn-result">
                                            Scopri se hai vinto
                                        </a>
                                    <?php elseif ($status === 'selecting'): ?>
                                        <span class="status-selecting">In corso...</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                        <?php 
                            $count++;
                        endforeach; 
                        wp_reset_postdata();
                    else: ?>
                        <div class="no-past-contests">
                            <p>📝 Nessun concorso precedente</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <?php if (is_user_logged_in()): ?>
        <section class="user-stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-icon">🏆</span>
                        <span class="stat-number"><?php echo instacontest_get_user_points(get_current_user_id()); ?></span>
                        <span class="stat-label">Punti Totali</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">🎯</span>
                        <span class="stat-number"><?php echo instacontest_get_user_participations(get_current_user_id()); ?></span>
                        <span class="stat-label">Partecipazioni</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">🥇</span>
                        <span class="stat-number"><?php echo instacontest_get_user_wins(get_current_user_id()); ?></span>
                        <span class="stat-label">Vittorie</span>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </main>
</div>

<!-- Bottom Navigation -->
<?php get_template_part('template-parts/bottom-navigation'); ?>

<!-- CSS Aggiuntivo per le nuove features -->
<style>
/* Immagine premio mini in overlay sulla card contest attivi */
.contest-card .contest-image {
    position: relative;
}

.prize-mini-image {
    position: absolute;
    bottom: 8px;
    right: 8px;
    z-index: 10;
}

/* Immagine premio mini per contest passati */
.contest-thumb {
    position: relative;
}

.prize-mini-thumb {
    position: absolute;
    top: 4px;
    right: 4px;
    z-index: 10;
}

/* Migliore CTA per contest terminati */
.btn-result {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-result:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.btn-result::before {
    content: "🎁";
    font-size: 10px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .prize-mini-image img {
        width: 10px;
        height: 10px;
    }
    
    .prize-mini-thumb img {
        width: 5px;
        height: 5px;
    }
}
</style>

<?php get_footer(); ?>
