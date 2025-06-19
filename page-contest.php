<?php
/**
 * Archive Contest Template - Homepage con stili Instagram
 * Versione aggiornata con gradienti Instagram
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main instacontest-homepage">
        
        <!-- Header con logo e stats utente -->
        <div class="homepage-header">
            <div class="container">
                <div class="header-content">
                    <h1 class="site-title">
                        <!-- Logo con gradiente Instagram -->
                        <span class="logo-icon instagram-gradient">🎯</span>
                        <span class="logo-gradient">InstaContest</span>
                    </h1>
                    
                    <?php if (is_user_logged_in()): ?>
                        <?php 
                        $user = wp_get_current_user();
                        $points = instacontest_get_user_points(get_current_user_id());
                        ?>
                        <div class="user-stats">
                            <div class="user-greeting">
                                <!-- Avatar con bordo gradiente -->
                                <div class="avatar-instagram">
                                    <?php echo get_avatar(get_current_user_id(), 40); ?>
                                </div>
                                <div class="user-info">
                                    <span class="greeting-text">CIAO @<?php echo strtoupper(esc_html($user->user_login)); ?></span>
                                    <span class="user-points instagram-text-gradient"><?php echo $points; ?> punti</span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="<?php echo wp_login_url(); ?>" class="btn-login instagram-gradient">Accedi</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contest Attivi -->
        <section class="contests-section active-contests">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="title-icon">🔥</span>
                        Concorsi aperti
                    </h2>
                    <span class="see-all instagram-text-gradient">Guarda tutti</span>
                </div>
                
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
                                    <?php if ($prize_image): ?>
                                        <img src="<?php echo esc_url($prize_image['sizes']['medium']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-image instagram-gradient-light">
                                            <span class="placeholder-icon">🎁</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badge APERTO con gradiente -->
                                    <div class="contest-status badge-active">APERTO</div>
                                </div>
                                
                                <div class="contest-info">
                                    <h3 class="contest-title"><?php echo esc_html($prize_name); ?></h3>
                                    <p class="contest-meta">
                                        <span class="prize-value">€<?php echo number_format($prize_value, 0, ',', '.'); ?></span>
                                        <span class="contest-points instagram-text-gradient">+<?php echo $participation_points; ?> punti</span>
                                    </p>
                                    
                                    <div class="contest-countdown-mini" 
                                         data-end-date="<?php echo date('Y-m-d H:i:s', strtotime($end_date)); ?>">
                                        <span class="countdown-label">Termina tra:</span>
                                        <span class="countdown-timer-mini"></span>
                                    </div>
                                    
                                    <!-- Pulsante con gradiente Instagram -->
                                    <a href="<?php echo get_permalink($contest_id); ?>" class="btn-contest-action btn-participate">
                                        PARTECIPA ORA
                                    </a>
                                </div>
                            </div>
                            
                        <?php endforeach; 
                        wp_reset_postdata();
                    else: ?>
                        <div class="no-contests">
                            <div class="no-contests-icon instagram-gradient">🔍</div>
                            <p>Nessun concorso attivo al momento</p>
                            <p class="no-contests-sub">Torna presto per nuove opportunità!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Contest Prossimi/In arrivo -->
        <section class="contests-section upcoming-contests">
            <div class="container">
                <h2 class="section-title">
                    <span class="title-icon">⏰</span>
                    Prossimamente
                </h2>
                
                <!-- Placeholder per contest futuri -->
                <div class="upcoming-cards">
                    <div class="upcoming-card">
                        <div class="upcoming-image">
                            <img src="placeholder-dazn.jpg" alt="DAZN">
                            <div class="upcoming-overlay">
                                <div class="countdown-display">
                                    <span class="countdown-digit">003</span>
                                    <span class="countdown-digit">22</span>
                                    <span class="countdown-digit">29</span>
                                    <span class="countdown-digit">57</span>
                                </div>
                            </div>
                        </div>
                        <div class="upcoming-info">
                            <h4>Abbonamento DAZN 3 mesi</h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contest Terminati -->
        <section class="contests-section past-contests">
            <div class="container">
                <h2 class="section-title">
                    <span class="title-icon">📋</span>
                    Contest terminati
                </h2>
                
                <div class="contests-list">
                    <?php 
                    $ended_contests = instacontest_get_ended_contests();
                    if ($ended_contests): 
                        $count = 0;
                        foreach ($ended_contests as $contest):
                            if ($count >= 5) break;
                            setup_postdata($contest);
                            $contest_id = $contest->ID;
                            $prize_name = get_field('prize_name', $contest_id);
                            $prize_image = get_field('prize_image', $contest_id);
                            $has_winner = instacontest_has_winner($contest_id);
                            $status = instacontest_get_contest_status($contest_id);
                            ?>
                            
                            <div class="contest-item past">
                                <div class="contest-thumb">
                                    <?php if ($prize_image): ?>
                                        <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-thumb instagram-gradient-light">🎁</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="contest-details">
                                    <h4 class="contest-name"><?php echo esc_html($prize_name); ?></h4>
                                    <p class="contest-date">
                                        Terminato il <?php echo get_the_date('d/m/Y'); ?>
                                    </p>
                                    <!-- Badge terminato -->
                                    <span class="badge-ended">TERMINATO</span>
                                </div>
                                
                                <div class="contest-action">
                                    <?php if ($status === 'completed'): ?>
                                        <a href="<?php echo get_permalink($contest_id); ?>" class="btn-result btn-participate">
                                            SCOPRI SE HAI VINTO
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
                        <span class="stat-icon instagram-gradient">🏆</span>
                        <span class="stat-number"><?php echo instacontest_get_user_points(get_current_user_id()); ?></span>
                        <span class="stat-label">Punti Totali</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon instagram-gradient">🎯</span>
                        <span class="stat-number"><?php echo instacontest_get_user_participations(get_current_user_id()); ?></span>
                        <span class="stat-label">Partecipazioni</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon instagram-gradient">🥇</span>
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

<style>
/* Stili specifici per questa pagina */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.see-all {
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
}

.logo-icon {
    display: inline-block;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    margin-right: 0.5rem;
}

.user-greeting {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.greeting-text {
    font-weight: 700;
    font-size: 1.125rem;
    color: #1f2937;
}

.user-points {
    font-size: 0.875rem;
    font-weight: 600;
}

.badge-ended {
    background: #fee2e2;
    color: #dc2626;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.contest-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1rem;
    min-width: 280px;
    position: relative;
}

.contest-image {
    position: relative;
    margin-bottom: 1rem;
}

.contest-image img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 0.75rem;
}

.placeholder-image {
    width: 100%;
    height: 160px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.contest-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.contest-grid {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: white;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
}

.stat-icon {
    display: inline-block;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin: 0 auto 0.5rem;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-label {
    display: block;
    font-size: 0.875rem;
    color: #6b7280;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

@media (max-width: 768px) {
    .contest-grid {
        gap: 0.75rem;
    }
    
    .contest-card {
        min-width: 240px;
    }
}
</style>

<?php get_footer(); ?>
