<?php
/**
 * Archive Contest Template - Homepage con Featured Image
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
                                    <!-- Featured Image principale -->
                                    <?php if (has_post_thumbnail($contest_id)): ?>
                                        <?php echo get_the_post_thumbnail($contest_id, 'medium', array(
                                            'class' => 'w-full h-full object-cover'
                                        )); ?>
                                    <?php elseif ($prize_image): ?>
                                        <!-- Fallback alla prize image se non c'è featured -->
                                        <img src="<?php echo esc_url($prize_image['sizes']['medium']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="placeholder-image">🎁</div>
                                    <?php endif; ?>
                                    
                                    <!-- Badge status -->
                                    <div class="contest-status active-badge">ATTIVO</div>
                                    
                                    <!-- Piccola immagine premio nell'angolo -->
                                    <?php if ($prize_image && has_post_thumbnail($contest_id)): ?>
                                        <div class="prize-thumbnail">
                                            <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                                 alt="Premio: <?php echo esc_attr($prize_name); ?>"
                                                 class="w-12 h-12 object-cover rounded-lg border-2 border-white shadow-lg">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="contest-info">
                                    <h3 class="contest-title"><?php echo esc_html(get_the_title($contest_id)); ?></h3>
                                    
                                    <!-- Info premio -->
                                    <div class="prize-info">
                                        <div class="prize-name"><?php echo esc_html($prize_name); ?></div>
                                        <div class="prize-meta">
                                            <span class="prize-value">€<?php echo number_format($prize_value, 0, ',', '.'); ?></span>
                                            <span class="contest-points">+<?php echo $participation_points; ?> punti</span>
                                        </div>
                                    </div>
                                    
                                    <div class="contest-countdown-mini" 
                                         data-end-date="<?php echo date('Y-m-d H:i:s', strtotime($end_date)); ?>">
                                        <span class="countdown-label">Termina tra:</span>
                                        <span class="countdown-timer-mini"></span>
                                    </div>
                                    
                                    <a href="<?php echo get_permalink($contest_id); ?>" class="btn-contest-action">
                                        <span class="btn-icon">🎯</span>
                                        <span>Partecipa Ora</span>
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
                            if ($count >= 8) break; // Mostra max 8 contest passati
                            setup_postdata($contest);
                            $contest_id = $contest->ID;
                            $prize_name = get_field('prize_name', $contest_id);
                            $prize_value = get_field('prize_value', $contest_id);
                            $prize_image = get_field('prize_image', $contest_id);
                            $has_winner = instacontest_has_winner($contest_id);
                            $status = instacontest_get_contest_status($contest_id);
                            ?>
                            
                            <div class="contest-item past">
                                <div class="contest-thumb">
                                    <!-- Featured Image come thumbnail -->
                                    <?php if (has_post_thumbnail($contest_id)): ?>
                                        <?php echo get_the_post_thumbnail($contest_id, 'thumbnail', array(
                                            'class' => 'w-full h-full object-cover'
                                        )); ?>
                                    <?php elseif ($prize_image): ?>
                                        <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="placeholder-thumb">🎁</div>
                                    <?php endif; ?>
                                    
                                    <!-- Piccola icona premio -->
                                    <?php if ($prize_image && has_post_thumbnail($contest_id)): ?>
                                        <div class="mini-prize-icon">
                                            <img src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                                 alt="Premio" class="w-6 h-6 object-cover rounded">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="contest-details">
                                    <h4 class="contest-name"><?php echo esc_html(get_the_title($contest_id)); ?></h4>
                                    <div class="contest-prize-info">
                                        <span class="small-prize-name"><?php echo esc_html($prize_name); ?></span>
                                        <span class="small-prize-value">€<?php echo number_format($prize_value, 0, ',', '.'); ?></span>
                                    </div>
                                    <p class="contest-date">
                                        <?php echo get_the_date('d/m/Y', $contest_id); ?>
                                    </p>
                                </div>
                                
                                <div class="contest-action">
                                    <?php if ($status === 'completed'): ?>
                                        <a href="<?php echo get_permalink($contest_id); ?>" class="btn-check-winner">
                                            <span class="btn-icon">🔍</span>
                                            <span class="btn-text">Scopri se hai vinto</span>
                                        </a>
                                    <?php elseif ($status === 'selecting'): ?>
                                        <span class="status-selecting">
                                            <span class="loading-dot"></span>
                                            <span>In corso...</span>
                                        </span>
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
                
                <!-- Link per vedere tutti i contest passati -->
                <?php if ($ended_contests && count($ended_contests) > 8): ?>
                    <div class="view-all-past">
                        <a href="#" class="btn-view-all" onclick="showAllPastContests()">
                            <span>Vedi tutti i contest terminati</span>
                            <span class="arrow">→</span>
                        </a>
                    </div>
                <?php endif; ?>
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

<!-- CSS Aggiuntivo per le nuove funzionalità -->
<style>
/* Prize thumbnail nell'angolo della card */
.contest-image {
    position: relative;
}

.prize-thumbnail {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 10;
}

/* Mini icona premio per contest passati */
.contest-thumb {
    position: relative;
}

.mini-prize-icon {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: white;
    border-radius: 4px;
    padding: 2px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Miglioramenti info premio */
.prize-info {
    margin: 12px 0;
}

.prize-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 4px;
}

.prize-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.prize-value {
    font-weight: 700;
    color: var(--success-color);
    font-size: 1rem;
}

.contest-points {
    background: var(--primary-gradient);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Contest passati - info premio piccola */
.contest-prize-info {
    margin: 4px 0;
}

.small-prize-name {
    font-size: 0.8rem;
    color: var(--gray-600);
    display: block;
}

.small-prize-value {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--success-color);
}

/* Pulsante "Scopri se hai vinto" */
.btn-check-winner {
    background: var(--instagram-gradient);
    color: white;
    padding: 8px 16px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(220, 39, 67, 0.2);
}

.btn-check-winner:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 39, 67, 0.3);
    color: white;
}

.btn-check-winner .btn-icon {
    font-size: 1rem;
}

/* Status in corso animato */
.status-selecting {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--gray-600);
    font-size: 0.85rem;
}

.loading-dot {
    width: 8px;
    height: 8px;
    background: var(--warning-color);
    border-radius: 50%;
    animation: pulse 1.5s ease-in-out infinite;
}

/* Link vedi tutti */
.view-all-past {
    text-align: center;
    margin-top: 24px;
}

.btn-view-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    padding: 12px 24px;
    border: 2px solid var(--primary-color);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.btn-view-all:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

.btn-view-all .arrow {
    transition: transform 0.3s ease;
}

.btn-view-all:hover .arrow {
    transform: translateX(4px);
}

/* Responsive */
@media (max-width: 768px) {
    .prize-thumbnail img {
        width: 40px;
        height: 40px;
    }
    
    .mini-prize-icon img {
        width: 20px;
        height: 20px;
    }
    
    .btn-check-winner {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
}
</style>

<!-- JavaScript per countdown e funzionalità extra -->
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
                const timerElement = element.querySelector('.countdown-timer-mini');
                if (timerElement) {
                    timerElement.textContent = 'Scaduto';
                    timerElement.style.color = '#ef4444';
                }
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
            
            const timerElement = element.querySelector('.countdown-timer-mini');
            if (timerElement) {
                timerElement.textContent = display;
            }
        }, 1000);
    });
});

// Funzione per mostrare tutti i contest passati
function showAllPastContests() {
    // Implementare logica per caricare via AJAX altri contest
    // Per ora redirect a una pagina dedicata
    alert('Funzionalità in arrivo: pagina con tutti i contest terminati');
}
</script>

<!-- Bottom Navigation -->
<?php get_template_part('template-parts/bottom-navigation'); ?>

<?php get_footer(); ?>
