<?php
/**
 * Archive Contest Template - Homepage
 * Mostra contest attivi e passati
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
                                    <?php if ($prize_image): ?>
                                        <img src="<?php echo esc_url($prize_image['sizes']['medium']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-image">🎁</div>
                                    <?php endif; ?>
                                    
                                    <div class="contest-status active-badge">ATTIVO</div>
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
                    <span class="title-icon">📋</span>
                    In arrivo
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
                                    <?php if ($prize_image): ?>
                                        <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-thumb">🎁</div>
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
                                            Risultati
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

<?php get_footer(); ?>