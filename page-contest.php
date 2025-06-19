<?php
/**
 * Template Name: Contest Homepage
 * Pagina contest modificabile da WordPress Admin
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main instacontest-homepage">
        
        <!-- CONTENUTO MODIFICABILE DA WORDPRESS ADMIN -->
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <!-- Header con contenuto della pagina WordPress -->
            <div class="editable-header">
                <div class="container">
                    
                    <!-- Immagine in evidenza (se caricata) -->
                    <?php if (has_post_thumbnail()): ?>
                        <div class="featured-image-section">
                            <?php the_post_thumbnail('large', array('class' => 'header-featured-image')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Titolo e contenuto della pagina -->
                    <div class="page-header-content">
                        <h1 class="page-title">
                            <span class="title-icon">🎯</span>
                            <?php the_title(); ?>
                        </h1>
                        
                        <!-- Contenuto modificabile dall'editor WordPress -->
                        <div class="page-intro-content">
                            <?php the_content(); ?>
                        </div>
                        
                        <!-- Stats utente se loggato -->
                        <?php if (is_user_logged_in()): ?>
                            <?php 
                            $user = wp_get_current_user();
                            $points = instacontest_get_user_points(get_current_user_id());
                            ?>
                            <div class="user-stats-header">
                                <div class="user-greeting">
                                    <span class="greeting-text">Ciao @<?php echo esc_html($user->user_login); ?>!</span>
                                    <span class="user-points"><?php echo $points; ?> punti</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="auth-prompt">
                                <p>🎯 <a href="<?php echo wp_login_url(); ?>">Accedi</a> per guadagnare punti e partecipare alla classifica!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
            
        <?php endwhile; endif; ?>

        <!-- CONTEST ATTIVI (automatici) -->
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
                                        Partecipa Ora
                                    </a>
                                </div>
                            </div>
                            
                        <?php endforeach; 
                        wp_reset_postdata();
                    else: ?>
                        <div class="no-contests">
                            <div class="no-contests-icon">🔍</div>
                            <h3>Nessun concorso attivo al momento</h3>
                            <p>Torna presto per nuove opportunità di vincere!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- CONTEST PASSATI -->
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

        <!-- SEZIONE AGGIUNTIVA MODIFICABILE (Campi Custom ACF) -->
        <?php 
        $bottom_content = get_field('bottom_section_content');
        $bottom_title = get_field('bottom_section_title');
        if ($bottom_content || $bottom_title): ?>
            <section class="editable-bottom-section">
                <div class="container">
                    <?php if ($bottom_title): ?>
                        <h2 class="section-title">
                            <span class="title-icon">💡</span>
                            <?php echo esc_html($bottom_title); ?>
                        </h2>
                    <?php endif; ?>
                    
                    <?php if ($bottom_content): ?>
                        <div class="bottom-content">
                            <?php echo wp_kses_post($bottom_content); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- STATS GENERALI (se utente loggato) -->
        <?php if (is_user_logged_in()): ?>
        <section class="user-stats-section">
            <div class="container">
                <h2 class="section-title">
                    <span class="title-icon">📊</span>
                    Le tue statistiche
                </h2>
                
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
                    <div class="stat-item">
                        <span class="stat-icon">📈</span>
                        <span class="stat-number">#<?php echo instacontest_get_user_position(get_current_user_id()); ?></span>
                        <span class="stat-label">Posizione</span>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </main>
</div>

<!-- Bottom Navigation -->
<?php get_template_part('template-parts/bottom-navigation'); ?>

<!-- JavaScript per countdown -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inizializza countdown per contest cards
    const countdownElements = document.querySelectorAll('[data-end-date]');
    
    countdownElements.forEach(function(element) {
        const endDate = new Date(element.getAttribute('data-end-date')).getTime();
        
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = endDate - now;
            
            if (distance < 0) {
                clearInterval(timer);
                element.querySelector('.countdown-timer-mini').textContent = 'Scaduto';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            let display = '';
            if (days > 0) {
                display = `${days}g ${hours}h`;
            } else if (hours > 0) {
                display = `${hours}h ${minutes}m`;
            } else {
                display = `${minutes}m`;
            }
            
            element.querySelector('.countdown-timer-mini').textContent = display;
        }, 1000);
    });
});
</script>

<?php get_footer(); ?>