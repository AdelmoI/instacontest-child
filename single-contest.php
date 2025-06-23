<?php
/**
 * Single Contest Template - Versione Migliorata
 * Design accattivante con animazioni e effetti
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
    $winner_username = get_field('winner_username', $contest_id);
?>

<!-- Particles Background -->
<div class="floating-particles-container">
    <?php for ($i = 0; $i < 9; $i++): ?>
        <div class="floating-particles" 
             style="left: <?php echo ($i + 1) * 10; ?>%; animation-delay: <?php echo $i * 0.5; ?>s;"></div>
    <?php endfor; ?>
</div>

<div id="primary" class="content-area">
    <main id="main" class="site-main contest-single">
        
        <!-- Header Contest -->
        <div class="contest-header">
            <div class="container">
                <div class="header-nav">
                    <a href="<?php echo get_post_type_archive_link('contest'); ?>" class="btn-back">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="ml-2">Concorsi</span>
                    </a>
                    <div class="header-actions">
                        <?php if ($status === 'active'): ?>
                            <span class="contest-status active">🔥 Contest Attivo</span>
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
                <div class="glass-effect">
                    <!-- Prize Image -->
                    <div class="prize-image-container">
                        <?php if ($prize_image): ?>
                            <img src="<?php echo esc_url($prize_image['sizes']['large']); ?>" 
                                 alt="<?php echo esc_attr($prize_name); ?>"
                                 class="prize-image glow-effect pulse-glow float-animation">
                        <?php else: ?>
                            <div class="prize-placeholder">
                                <span class="text-6xl">🎁</span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Floating Badge -->
                        <div class="floating-badge">
                            TOP PRIZE! 🏆
                        </div>
                    </div>
                    
                    <!-- Prize Info -->
                    <div class="prize-info">
                        <h1 class="prize-name"><?php echo esc_html($prize_name); ?></h1>
                        <p class="prize-description">
                            <?php echo esc_html(get_the_excerpt()); ?>
                        </p>
                        <div class="prize-value-badge">
                            Valore: €<?php echo number_format($prize_value, 0, ',', '.'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($status === 'active'): ?>
            <!-- CONTEST ATTIVO -->
            
            <!-- Countdown Section -->
            <div class="countdown-section">
                <div class="container">
                    <div class="glass-effect">
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
                </div>
            </div>

            <!-- Participation Section -->
            <div class="participation-section">
                <div class="container">
                    <div class="glass-effect">
                        <h3 class="participation-title">
                            <span class="text-2xl">🎯</span> Come partecipare
                        </h3>
                        
                        <div class="instructions-list">
                            <?php 
                            $instruction_lines = explode("\n", strip_tags($instructions));
                            $step = 1;
                            foreach ($instruction_lines as $line): 
                                if (trim($line)): ?>
                                    <div class="instruction-item">
                                        <div class="instruction-number"><?php echo $step; ?></div>
                                        <div class="instruction-content">
                                            <p class="instruction-title"><?php echo esc_html(trim($line)); ?></p>
                                            <p class="instruction-description">
                                                <?php 
                                                // Aggiungi descrizioni dinamiche basate sul contenuto
                                                if (stripos($line, 'like') !== false || stripos($line, 'piace') !== false) {
                                                    echo 'Clicca il ❤️ sul post Instagram';
                                                } elseif (stripos($line, 'comment') !== false || stripos($line, 'commenta') !== false) {
                                                    echo 'Scrivi un commento creativo';
                                                } elseif (stripos($line, 'tag') !== false || stripos($line, 'tagga') !== false) {
                                                    echo 'Usa @nomeutente nei commenti';
                                                } else {
                                                    echo 'Segui le istruzioni nel post';
                                                }
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php 
                                    $step++;
                                endif;
                            endforeach; ?>
                        </div>

                        <?php if (is_user_logged_in()): ?>
                            <!-- Points Info -->
                            <div class="points-info">
                                <div class="points-card">
                                    <span class="points-icon">⭐</span>
                                    <div class="points-details">
                                        <p class="points-text">Partecipando guadagni</p>
                                        <p class="points-number">+<?php echo $participation_points; ?> punti</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Participate Button -->
                        <a href="<?php echo esc_url($instagram_url); ?>" 
                           target="_blank" 
                           class="btn-participate ripple-effect"
                           onclick="instacontestTrackParticipation(<?php echo $contest_id; ?>)">
                            <div class="instagram-icon">
                                <svg class="w-6 h-6" fill="white" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </div>
                            <span>Partecipa su Instagram</span>
                        </a>
                        
                        <p class="participate-note">
                            Verrai reindirizzato al post Instagram per partecipare
                        </p>
                    </div>
                </div>
            </div>

            <!-- Participants Counter -->
            <div class="participants-section">
                <div class="container">
                    <div class="glass-effect">
                        <div class="participants-counter">
                            <div class="avatar-group">
                                <div class="participant-avatar avatar-1">😊</div>
                                <div class="participant-avatar avatar-2">🤩</div>
                                <div class="participant-avatar avatar-3">😍</div>
                                <div class="participant-avatar avatar-more">+<?php echo rand(100, 999); ?></div>
                            </div>
                            <div class="participants-info">
                                <p class="participants-count"><?php echo number_format(rand(1500, 3000), 0, ',', '.'); ?> persone</p>
                                <p class="participants-label">stanno partecipando</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($status === 'selecting'): ?>
            <!-- SELEZIONE VINCITORE IN CORSO -->
            <div class="selecting-section">
                <div class="container">
                    <div class="glass-effect text-center">
                        <div class="text-6xl mb-6 float-animation">🎲</div>
                        <h2 class="text-white text-2xl font-bold mb-4">Contest Terminato!</h2>
                        <p class="text-white text-opacity-80 mb-6">Stiamo selezionando il vincitore...</p>
                        <div class="flex justify-center space-x-2 mb-6">
                            <div class="w-3 h-3 bg-white rounded-full animate-bounce"></div>
                            <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                            <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                        </div>
                        <p class="text-white text-opacity-70">Il vincitore verrà annunciato a breve. Torna a controllare!</p>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- CONTEST COMPLETATO - FORM VINCITORE -->
            <div class="completed-section">
                <div class="container">
                    <div class="glass-effect">
                        <div class="text-center mb-8">
                            <div class="text-6xl mb-4">🏆</div>
                            <h2 class="text-white text-2xl font-bold mb-2">Contest Terminato!</h2>
                            <p class="text-white text-opacity-80">Il vincitore è stato selezionato. Scopri se sei tu!</p>
                        </div>

                        <!-- Form verifica vincitore -->
                        <div class="winner-form-section">
                            <h3 class="text-white text-xl font-bold mb-4 text-center">Verifica la tua partecipazione</h3>
                            <p class="text-white text-opacity-70 text-center mb-6">Inserisci i tuoi dati per scoprire se hai vinto</p>
                            
                            <form method="post" action="" class="winner-form space-y-4">
                                <?php wp_nonce_field('instacontest_check_winner', 'instacontest_check_winner_nonce'); ?>
                                <input type="hidden" name="contest_id" value="<?php echo $contest_id; ?>">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label for="nome" class="block text-white text-sm font-medium mb-2">Nome</label>
                                        <input type="text" id="nome" name="nome" required 
                                               class="w-full px-4 py-3 rounded-xl bg-white bg-opacity-10 border border-white border-opacity-20 text-white placeholder-white placeholder-opacity-60 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                                    </div>
                                    <div class="form-group">
                                        <label for="cognome" class="block text-white text-sm font-medium mb-2">Cognome</label>
                                        <input type="text" id="cognome" name="cognome" required 
                                               class="w-full px-4 py-3 rounded-xl bg-white bg-opacity-10 border border-white border-opacity-20 text-white placeholder-white placeholder-opacity-60 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="block text-white text-sm font-medium mb-2">Email</label>
                                    <input type="email" id="email" name="email" required 
                                           class="w-full px-4 py-3 rounded-xl bg-white bg-opacity-10 border border-white border-opacity-20 text-white placeholder-white placeholder-opacity-60 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                                </div>
                                
                                <div class="form-group">
                                    <label for="telefono" class="block text-white text-sm font-medium mb-2">Telefono</label>
                                    <input type="tel" id="telefono" name="telefono" required 
                                           class="w-full px-4 py-3 rounded-xl bg-white bg-opacity-10 border border-white border-opacity-20 text-white placeholder-white placeholder-opacity-60 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                                </div>
                                
                                <div class="form-group">
                                    <label for="username_ig" class="block text-white text-sm font-medium mb-2">Username Instagram</label>
                                    <input type="text" id="username_ig" name="username_ig" 
                                           placeholder="@tusername" required 
                                           class="w-full px-4 py-3 rounded-xl bg-white bg-opacity-10 border border-white border-opacity-20 text-white placeholder-white placeholder-opacity-60 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                                </div>
                                
                                <button type="submit" class="btn-participate w-full mt-6">
                                    <span class="text-2xl mr-2">🔍</span>
                                    <span>Verifica risultato</span>
                                </button>
                            </form>
                        </div>

                        <!-- Risultato verifica -->
                        <?php if (isset($_GET['winner_check'])): ?>
                            <div class="winner-result-section mt-8">
                                <?php if ($_GET['winner_check'] === 'won'): ?>
                                    <div class="result-card winner">
                                        <div class="result-icon">🎉</div>
                                        <h2 class="result-title">CONGRATULAZIONI!</h2>
                                        <h3 class="result-subtitle">HAI VINTO!</h3>
                                        <p class="result-message">Verrai contattato presto per la consegna del premio.</p>
                                        <?php if (is_user_logged_in()): ?>
                                            <div class="points-earned">
                                                <span class="text-2xl">⭐</span>
                                                <span>Hai guadagnato <?php echo $winner_points; ?> punti extra!</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <script>
                                        // Lancia confetti per il vincitore
                                        setTimeout(() => {
                                            for (let i = 0; i < 50; i++) {
                                                setTimeout(() => {
                                                    const confetti = document.createElement('div');
                                                    confetti.className = 'confetti';
                                                    confetti.style.left = Math.random() * 100 + 'vw';
                                                    confetti.style.animationDelay = Math.random() * 3 + 's';
                                                    document.body.appendChild(confetti);
                                                    setTimeout(() => confetti.remove(), 3000);
                                                }, i * 100);
                                            }
                                        }, 500);
                                    </script>
                                <?php elseif ($_GET['winner_check'] === 'lost'): ?>
                                    <div class="result-card loser">
                                        <div class="result-icon">😔</div>
                                        <h2 class="result-title">Mi dispiace</h2>
                                        <h3 class="result-subtitle">Non hai vinto questa volta</h3>
                                        <p class="result-message">Continua a partecipare ai nostri contest!</p>
                                        <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
                                           class="btn-participate inline-block mt-4">
                                            Vedi altri concorsi
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Prize Details -->
        <div class="prize-details-section">
            <div class="container">
                <div class="glass-effect">
                    <h3 class="prize-details-title">
                        <span class="text-2xl">📱</span> Dettagli Premio
                    </h3>
                    
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

                    <?php if (is_user_logged_in()): ?>
                        <div class="winner-bonus">
                            <div class="winner-bonus-content">
                                <span class="winner-bonus-icon">🏆</span>
                                <div>
                                    <p class="winner-bonus-text">Vincendo ottieni</p>
                                    <p class="winner-bonus-points">+<?php echo $winner_points; ?> punti bonus!</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Enhanced JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced countdown functionality
    function updateCountdown() {
        const countdownElement = document.querySelector('[data-end-date]');
        if (!countdownElement) return;
        
        const endDate = new Date(countdownElement.getAttribute('data-end-date')).getTime();
        const now = new Date().getTime();
        const distance = endDate - now;
        
        if (distance < 0) {
            countdownElement.innerHTML = '<div class="text-white text-xl font-bold">Contest Terminato!</div>';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        const daysEl = countdownElement.querySelector('.days');
        const hoursEl = countdownElement.querySelector('.hours');
        const minutesEl = countdownElement.querySelector('.minutes');
        const secondsEl = countdownElement.querySelector('.seconds');
        
        if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
        if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
        if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
        if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
    }
    
    // Update countdown every second
    setInterval(updateCountdown, 1000);
    updateCountdown();

    // Enhanced ripple effect
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.ripple-effect');
        if (button) {
            const ripple = document.createElement('div');
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ripple.style.cssText = `
                position: absolute;
                background: rgba(255,255,255,0.5);
                border-radius: 50%;
                width: 100px;
                height: 100px;
                left: ${x - 50}px;
                top: ${y - 50}px;
                animation: ripple 0.6s ease-out;
                pointer-events: none;
                z-index: 1000;
            `;
            
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        }
    });

    // Add ripple animation
    if (!document.querySelector('#ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            @keyframes ripple {
                0% { transform: scale(0); opacity: 1; }
                100% { transform: scale(1); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
});

// Track participation function
function instacontestTrackParticipation(contestId) {
    <?php if (is_user_logged_in()): ?>
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=instacontest_track_participation&contest_id=' + contestId + '&nonce=<?php echo wp_create_nonce('track_participation'); ?>'
    }).then(response => response.json()).then(data => {
        if (data.success) {
            // Mostra notifica di successo se necessario
            console.log('Partecipazione tracciata!');
        }
    });
    <?php endif; ?>
}
</script>

<!-- Bottom Navigation -->
<?php get_template_part('template-parts/bottom-navigation'); ?>

<?php
endwhile;
get_footer(); ?>
