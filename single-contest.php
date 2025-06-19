<?php
/**
 * Single Contest Template
 * Gestisce contest attivi, in selezione e completati
 */

get_header(); 

while (have_posts()) : the_post();
    $contest_id = get_the_ID();
    $status = instacontest_get_contest_status($contest_id);
    $prize_name = get_field('prize_name', $contest_id);
    $prize_value = get_field('prize_value', $contest_id);
    $prize_image = get_field('prize_image', $contest_id);
    $end_date = get_field('contest_end_date', $contest_id);
    $instructions = get_field('participation_instructions', $contest_id);
    $instagram_url = get_field('instagram_post_url', $contest_id);
    $participation_points = get_field('participation_points', $contest_id) ?: 5;
    $winner_points = get_field('winner_points', $contest_id) ?: 50;
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main contest-single">
        
        <!-- Header Contest -->
        <div class="contest-header">
            <div class="container">
                <div class="header-nav">
                    <a href="<?php echo get_post_type_archive_link('contest'); ?>" class="btn-back">
                        ← Concorsi
                    </a>
                    <div class="header-actions">
                        <?php if ($status === 'active'): ?>
                            <span class="contest-status active">🔥 Attivo</span>
                        <?php elseif ($status === 'selecting'): ?>
                            <span class="contest-status selecting">⏳ In selezione</span>
                        <?php else: ?>
                            <span class="contest-status completed">✅ Completato</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prize Hero Section -->
        <div class="prize-hero">
            <div class="container">
                <div class="prize-image-container">
                    <?php if ($prize_image): ?>
                        <img src="<?php echo esc_url($prize_image['sizes']['large']); ?>" 
                             alt="<?php echo esc_attr($prize_name); ?>"
                             class="prize-image">
                    <?php else: ?>
                        <div class="prize-placeholder">
                            <span class="placeholder-icon">🎁</span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Overlay con informazioni premio -->
                    <div class="prize-overlay">
                        <h1 class="prize-name"><?php echo esc_html($prize_name); ?></h1>
                        <p class="prize-value">Valore: €<?php echo number_format($prize_value, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contest Content -->
        <div class="contest-content">
            <div class="container">
                
                <?php if ($status === 'active'): ?>
                    <!-- CONTEST ATTIVO -->
                    <div class="contest-active-content">
                        
                        <!-- Countdown -->
                        <div class="countdown-section">
                            <h2 class="countdown-title">⏰ Termina il</h2>
                            <div class="countdown-date"><?php echo instacontest_format_contest_date($end_date); ?></div>
                            <div id="countdown-<?php echo $contest_id; ?>" 
                                 class="countdown-timer" 
                                 data-end-date="<?php echo date('Y-m-d H:i:s', strtotime($end_date)); ?>">
                                <div class="countdown-item">
                                    <span class="countdown-number days">00</span>
                                    <span class="countdown-label">Giorni</span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-number hours">00</span>
                                    <span class="countdown-label">Ore</span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-number minutes">00</span>
                                    <span class="countdown-label">Min</span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-number seconds">00</span>
                                    <span class="countdown-label">Sec</span>
                                </div>
                            </div>
                        </div>

                        <!-- Come partecipare -->
                        <div class="participation-section">
                            <h3 class="section-title">Come partecipare</h3>
                            <div class="instructions-content">
                                <?php echo wpautop($instructions); ?>
                            </div>
                            
                            <?php if (is_user_logged_in()): ?>
                                <div class="points-info">
                                    <div class="points-card">
                                        <span class="points-icon">🎯</span>
                                        <div class="points-details">
                                            <span class="points-text">Partecipando guadagni</span>
                                            <span class="points-number"><?php echo $participation_points; ?> punti</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Pulsante Partecipa -->
                        <div class="participate-section">
                            <a href="<?php echo esc_url($instagram_url); ?>" 
                               target="_blank" 
                               class="btn-participate"
                               onclick="instacontestTrackParticipation(<?php echo $contest_id; ?>)">
                                <span class="btn-icon">📸</span>
                                <span class="btn-text">Partecipa su Instagram</span>
                            </a>
                            <p class="participate-note">
                                Clicca il pulsante per essere reindirizzato al post Instagram
                            </p>
                        </div>

                    </div>

                <?php elseif ($status === 'selecting'): ?>
                    <!-- SELEZIONE VINCITORE IN CORSO -->
                    <div class="contest-selecting-content">
                        <div class="selecting-animation">
                            <div class="animation-icon">🎲</div>
                            <h2>Contest Terminato!</h2>
                            <p class="selecting-text">Stiamo selezionando il vincitore...</p>
                            <div class="loading-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <p class="selecting-sub">Il vincitore verrà annunciato a breve. Torna a controllare!</p>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- CONTEST COMPLETATO - FORM VINCITORE -->
                    <div class="contest-completed-content">
                        
                        <!-- Annuncio vincitore -->
                        <div class="winner-announcement">
                            <div class="announcement-icon">🏆</div>
                            <h2>Contest Terminato!</h2>
                            <p>Il vincitore è stato selezionato. Scopri se sei tu!</p>
                        </div>

                        <!-- Form verifica vincitore -->
                        <div class="winner-form-section">
                            <h3>Verifica la tua partecipazione</h3>
                            <p class="form-intro">Inserisci i tuoi dati per scoprire se hai vinto</p>
                            
                            <form method="post" action="" class="winner-form">
                                <?php wp_nonce_field('instacontest_check_winner', 'instacontest_check_winner_nonce'); ?>
                                <input type="hidden" name="contest_id" value="<?php echo $contest_id; ?>">
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="nome">Nome</label>
                                        <input type="text" id="nome" name="nome" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cognome">Cognome</label>
                                        <input type="text" id="cognome" name="cognome" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="telefono">Telefono</label>
                                    <input type="tel" id="telefono" name="telefono" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="username_ig">Username Instagram</label>
                                    <input type="text" id="username_ig" name="username_ig" 
                                           placeholder="@tusername" required>
                                </div>
                                
                                <button type="submit" class="btn-verify">
                                    <span class="btn-icon">🔍</span>
                                    <span class="btn-text">Verifica risultato</span>
                                </button>
                            </form>
                        </div>

                        <!-- Risultato verifica -->
                        <?php if (isset($_GET['winner_check'])): ?>
                            <div class="winner-result-section">
                                <?php if ($_GET['winner_check'] === 'won'): ?>
                                    <div class="result-card winner">
                                        <div class="result-icon">🎉</div>
                                        <h2>CONGRATULAZIONI!</h2>
                                        <h3>HAI VINTO!</h3>
                                        <p>Verrai contattato presto per la consegna del premio.</p>
                                        <?php if (is_user_logged_in()): ?>
                                            <div class="points-earned">
                                                <span class="points-icon">⭐</span>
                                                <span>Hai guadagnato <?php echo $winner_points; ?> punti extra!</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($_GET['winner_check'] === 'lost'): ?>
                                    <div class="result-card loser">
                                        <div class="result-icon">😔</div>
                                        <h2>Mi dispiace</h2>
                                        <h3>Non hai vinto questa volta</h3>
                                        <p>Continua a partecipare ai nostri contest!</p>
                                        <a href="<?php echo get_post_type_archive_link('contest'); ?>" class="btn-back-contests">
                                            Vedi altri concorsi
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

                <!-- Dettagli Premio -->
                <div class="prize-details-section">
                    <h3>Dettagli premio</h3>
                    <div class="prize-specs">
                        <div class="spec-item">
                            <span class="spec-label">Valore</span>
                            <span class="spec-value">€<?php echo number_format($prize_value, 0, ',', '.'); ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Categoria</span>
                            <span class="spec-value">Tecnologia</span>
                        </div>
                        <?php if (is_user_logged_in()): ?>
                            <div class="spec-item">
                                <span class="spec-label">Punti partecipazione</span>
                                <span class="spec-value">+<?php echo $participation_points; ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Punti vincita</span>
                                <span class="spec-value">+<?php echo $winner_points; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Partecipanti -->
                <?php if ($status !== 'selecting'): ?>
                <div class="participants-section">
                    <h3>🎯 2.547 partecipanti</h3>
                    <div class="participants-avatars">
                        <!-- Placeholder per avatars partecipanti -->
                        <div class="avatar-group">
                            <div class="avatar">👤</div>
                            <div class="avatar">👤</div>
                            <div class="avatar">👤</div>
                            <div class="avatar more">+<?php echo rand(100, 999); ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </main>
</div>

<!-- JavaScript per tracking partecipazione -->
<script>
function instacontestTrackParticipation(contestId) {
    <?php if (is_user_logged_in()): ?>
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=instacontest_track_participation&contest_id=' + contestId + '&nonce=<?php echo wp_create_nonce('track_participation'); ?>'
    });
    <?php endif; ?>
}
</script>

<!-- Bottom Navigation -->
<?php get_template_part('template-parts/bottom-navigation'); ?>

<?php
endwhile;
get_footer(); ?>