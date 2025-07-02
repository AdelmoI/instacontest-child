<?php
/**
 * Single Contest Template - Stile Homepage Non Boxato
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
    $instagram_url = get_field('instagram_post_url', $contest_id);
    $participation_points = get_field('participation_points', $contest_id) ?: 5;
    $winner_points = get_field('winner_points', $contest_id) ?: 50;
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main contest-single">
        
        <!-- Header con navigazione -->
        <div class="bg-white/10 backdrop-blur-sm border-b border-white/20">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
                       class="flex items-center space-x-2 text-white hover:text-purple-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="font-medium">Concorsi</span>
                    </a>
                    
                    <!-- Status badge -->
                    <div class="flex items-center">
                        <?php if ($status === 'active'): ?>
                            <span class="bg-green-500 px-3 py-1 rounded-full text-white text-sm font-semibold flex items-center space-x-1">
                                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                <span>Attivo</span>
                            </span>
                        <?php elseif ($status === 'selecting'): ?>
                            <span class="bg-yellow-500 px-3 py-1 rounded-full text-white text-sm font-semibold flex items-center space-x-1">
                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                <span>In selezione</span>
                            </span>
                        <?php else: ?>
                            <span class="bg-gray-500 px-3 py-1 rounded-full text-white text-sm font-semibold flex items-center space-x-1">
                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                <span>Completato</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section con Featured Image -->
        <?php if (has_post_thumbnail()): ?>
        <section class="hero-section relative">
            <div class="relative h-64 md:h-80 lg:h-96 overflow-hidden">
                <?php the_post_thumbnail('large', array(
                    'class' => 'w-full h-full object-cover'
                )); ?>
                
                <!-- Overlay gradiente -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                
                <!-- Titolo sovrapposto -->
                <div class="absolute inset-x-0 bottom-0 p-6">
                    <div class="container mx-auto">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-2 leading-tight">
                            <?php the_title(); ?>
                        </h1>
                        <div class="flex items-center space-x-2 text-white/90">
                            <span class="text-sm md:text-base">Contest</span>
                            <span class="w-1 h-1 bg-white/50 rounded-full"></span>
                            <span class="text-sm md:text-base"><?php echo get_the_date('d/m/Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php else: ?>
        <!-- Header senza immagine -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 py-12">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
                    <?php the_title(); ?>
                </h1>
                <p class="text-white/90 text-lg">Contest del <?php echo get_the_date('d/m/Y'); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contenuto principale -->
        <div class="bg-white">
            <div class="container mx-auto px-4 py-8">
                
                <!-- Layout responsive: mobile stack, desktop grid -->
                <div class="lg:grid lg:grid-cols-3 lg:gap-8 space-y-8 lg:space-y-0">
                    
                    <!-- Colonna principale (2/3 su desktop) -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <?php if ($status === 'active'): ?>
                            <!-- CONTEST ATTIVO -->
                            
                            <!-- Countdown Timer - UNA RIGA SU MOBILE -->
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6">
                                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 flex items-center space-x-2">
                                    <span class="text-2xl">⏰</span>
                                    <span>Termina il</span>
                                </h2>
                                <div class="text-lg text-gray-600 mb-6">
                                    <?php echo instacontest_format_contest_date($end_date); ?>
                                </div>
                                
                                <!-- Grid countdown - 4 colonne sempre -->
                                <div id="countdown-<?php echo $contest_id; ?>" 
                                     class="grid grid-cols-4 gap-2 md:gap-4"
                                     data-end-date="<?php echo date('Y-m-d H:i:s', strtotime($end_date)); ?>">
                                    <div class="bg-white rounded-xl p-2 md:p-4 text-center shadow-sm">
                                        <div class="text-lg md:text-2xl lg:text-3xl font-bold text-purple-600 countdown-number days">00</div>
                                        <div class="text-xs md:text-sm text-gray-600 font-medium">Giorni</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-2 md:p-4 text-center shadow-sm">
                                        <div class="text-lg md:text-2xl lg:text-3xl font-bold text-purple-600 countdown-number hours">00</div>
                                        <div class="text-xs md:text-sm text-gray-600 font-medium">Ore</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-2 md:p-4 text-center shadow-sm">
                                        <div class="text-lg md:text-2xl lg:text-3xl font-bold text-purple-600 countdown-number minutes">00</div>
                                        <div class="text-xs md:text-sm text-gray-600 font-medium">Min</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-2 md:p-4 text-center shadow-sm">
                                        <div class="text-lg md:text-2xl lg:text-3xl font-bold text-purple-600 countdown-number seconds">00</div>
                                        <div class="text-xs md:text-sm text-gray-600 font-medium">Sec</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Come partecipare -->
                            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center space-x-3">
                                    <span class="text-2xl">📋</span>
                                    <span>COME PARTECIPARE</span>
                                </h3>
                                
                                <!-- Lista azioni numerata -->
                                <div class="mt-6 space-y-0">
                                    <?php
                                    // Raccoglie tutte le azioni compilate
                                    $actions = array();
                                    for ($i = 1; $i <= 5; $i++) {
                                        $action = get_field('action_' . $i, $contest_id);
                                        if (!empty($action)) {
                                            $actions[] = $action;
                                        }
                                    }
                                    
                                    // Mostra le azioni se esistono
                                    if (!empty($actions)): 
                                        foreach ($actions as $index => $action): 
                                            $number = $index + 1;
                                    ?>
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm"><?php echo $number; ?></span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-gray-700 font-medium leading-relaxed"><?php echo esc_html($action); ?></p>
                                            </div>
                                        </div>
                                    <?php 
                                        endforeach;
                                    else: ?>
                                        <p class="text-gray-500 italic">Istruzioni di partecipazione non ancora disponibili.</p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Punti info per utenti loggati -->
                                <?php if (is_user_logged_in()): ?>
                                    <div class="mt-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="text-3xl">🎯</div>
                                            <div>
                                                <div class="text-sm text-gray-600">Partecipando guadagni</div>
                                                <div class="text-xl font-bold text-purple-600">
                                                    +<?php echo $participation_points; ?> punti
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Pulsante Partecipa con immagine Instagram -->
                            <div class="text-center space-y-4">
                                <a href="<?php echo esc_url($instagram_url); ?>" 
                                   target="_blank" 
                                   class="block w-full btn-participate font-bold py-3 rounded-xl text-sm text-center flex items-center justify-center space-x-3"
                                   onclick="instacontestTrackParticipation(<?php echo $contest_id; ?>)">
                                    <img src="https://www.instacontest.it/wp-content/uploads/2025/06/instagram-new.png" 
                                         alt="Instagram" 
                                         class="w-6 h-6 md:w-8 md:h-8">
                                    <span class="text-lg">PARTECIPA SU INSTAGRAM</span>
                                </a>
                                <p class="text-gray-500 text-sm">
                                    Sarai reindirizzato al post Instagram
                                </p>
                            </div>

                        <?php elseif ($status === 'selecting'): ?>
                            <!-- SELEZIONE VINCITORE -->
                            <div class="text-center py-12 space-y-6">
                                <div class="text-8xl animate-bounce">🎲</div>
                                <div class="space-y-2">
                                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Contest Terminato!</h2>
                                    <p class="text-lg text-gray-600">Stiamo selezionando il vincitore...</p>
                                </div>
                                
                                <!-- Loading dots animati -->
                                <div class="flex justify-center space-x-1">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
                                    <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse delay-100"></div>
                                    <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse delay-200"></div>
                                </div>
                                
                                <p class="text-gray-500">
                                    Il vincitore verrà annunciato a breve. Torna a controllare!
                                </p>
                            </div>

                        <?php else: ?>
                            <!-- CONTEST COMPLETATO - FORM VINCITORE -->
                            <div class="contest-completed-content">
                                
                                <!-- Annuncio vincitore -->
                                <div class="winner-announcement">
                                    <div class="flex flex-col items-center text-center py-8">
                                        <div class="text-6xl mb-4">🏆</div>
                                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Contest Terminato!</h2>
                                        <p class="text-gray-600">Il vincitore è stato selezionato. Scopri se sei tu!</p>
                                    </div>
                                </div>

                                <?php if (!isset($_GET['winner_check'])): ?>
                                    <!-- MOSTRA FORM SOLO SE NON C'È RISULTATO -->
                                    <div class="winner-form-section bg-white rounded-lg shadow-lg p-6 mx-4 mb-6">
                                        <div class="text-center mb-6">
                                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Verifica la tua partecipazione</h3>
                                            <p class="text-gray-600">Inserisci i tuoi dati per scoprire se hai vinto</p>
                                        </div>
                                        
                                        <form method="post" action="" class="space-y-4">
                                            <?php wp_nonce_field('instacontest_check_winner', 'instacontest_check_winner_nonce'); ?>
                                            <input type="hidden" name="contest_id" value="<?php echo $contest_id; ?>">
                                            
                                            <!-- Prima riga: Nome e Cognome -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                                                    <input type="text" 
                                                        id="nome" 
                                                        name="nome" 
                                                        required
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                                </div>
                                                <div>
                                                    <label for="cognome" class="block text-sm font-medium text-gray-700 mb-1">Cognome</label>
                                                    <input type="text" 
                                                        id="cognome" 
                                                        name="cognome" 
                                                        required
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                                </div>
                                            </div>
                                            
                                            <!-- Email -->
                                            <div>
                                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                <input type="email" 
                                                    id="email" 
                                                    name="email" 
                                                    required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                            </div>
                                            
                                            <!-- Telefono -->
                                            <div>
                                                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                                                <input type="tel" 
                                                    id="telefono" 
                                                    name="telefono" 
                                                    required
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                            </div>
                                            
                                            <!-- Username Instagram -->
                                            <div>
                                                <label for="username_ig" class="block text-sm font-medium text-gray-700 mb-1">Username Instagram</label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">@</span>
                                                    <input type="text" 
                                                        id="username_ig" 
                                                        name="username_ig" 
                                                        placeholder="tusername" 
                                                        required
                                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                                </div>
                                            </div>
                                            
                                            <!-- Pulsante verifica -->
                                            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold py-4 px-6 rounded-lg hover:from-blue-600 hover:to-cyan-600 transform hover:scale-105 transition-all duration-200 shadow-lg flex items-center justify-center gap-2">
                                                <i class="fas fa-search"></i>
                                                <span>Verifica risultato</span>
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <!-- MOSTRA RISULTATO SOLO SE C'È IL PARAMETRO -->
                                    <div class="winner-result-section mx-4">
                                        <?php if ($_GET['winner_check'] === 'won'): ?>
                                            <!-- HA VINTO -->
                                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-8 text-center shadow-lg">
                                                <div class="text-6xl mb-4">🎉</div>
                                                <h2 class="text-3xl font-bold text-green-800 mb-2">CONGRATULAZIONI!</h2>
                                                <h3 class="text-xl font-semibold text-green-700 mb-4">HAI VINTO!</h3>
                                                <p class="text-green-600 mb-6">Verrai contattato presto per la consegna del premio.</p>
                                                
                                                <?php if (is_user_logged_in()): ?>
                                                    <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4 mb-4">
                                                        <div class="flex items-center justify-center gap-2 text-yellow-800">
                                                            <i class="fas fa-star text-yellow-500"></i>
                                                            <span class="font-semibold">Hai guadagnato <?php echo $winner_points; ?> punti extra!</span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
                                                class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                                    <i class="fas fa-trophy"></i>
                                                    <span>Vedi altri concorsi</span>
                                                </a>
                                            </div>
                                            
                                        <?php elseif ($_GET['winner_check'] === 'lost'): ?>
                                            <!-- HA PERSO -->
                                            <div class="bg-gradient-to-br from-gray-50 to-slate-50 border-2 border-gray-200 rounded-xl p-8 text-center shadow-lg">
                                                <div class="text-6xl mb-4">😔</div>
                                                <h2 class="text-2xl font-bold text-gray-700 mb-2">Mi dispiace</h2>
                                                <h3 class="text-lg font-semibold text-gray-600 mb-4">Non hai vinto questa volta</h3>
                                                <p class="text-gray-500 mb-6">Continua a partecipare ai nostri contest per aumentare le tue possibilità!</p>
                                                
                                                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                                    <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
                                                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                                        <i class="fas fa-list"></i>
                                                        <span>Vedi altri concorsi</span>
                                                    </a>
                                                    
                                                    <button onclick="window.location.reload()" 
                                                            class="inline-flex items-center gap-2 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-semibold">
                                                        <i class="fas fa-redo"></i>
                                                        <span>Riprova verifica</span>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Sidebar (1/3 su desktop) -->
                    <div class="lg:col-span-1 space-y-6">
                        
                        <!-- Card Premio con immagine -->
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                            <!-- Immagine premio -->
                            <?php if ($prize_image): ?>
                                <div class="aspect-square overflow-hidden">
                                    <img src="<?php echo esc_url($prize_image['sizes']['medium']); ?>" 
                                         alt="<?php echo esc_attr($prize_name); ?>"
                                         class="w-full h-full object-cover">
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center space-x-2">
                                    <span class="text-2xl">🎁</span>
                                    <span>Premio in palio</span>
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800"><?php echo esc_html($prize_name); ?></h4>
                                        <p class="text-2xl font-bold text-purple-600">
                                            €<?php echo number_format($prize_value, 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dettagli Punteggio (se loggato) -->
                        <?php if (is_user_logged_in()): ?>
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center space-x-2">
                                <span class="text-xl">🏆</span>
                                <span>Punteggi</span>
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Partecipazione</span>
                                    <span class="font-bold text-purple-600">+<?php echo $participation_points; ?> punti</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Vincita</span>
                                    <span class="font-bold text-yellow-600">+<?php echo $winner_points; ?> punti</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>



                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- JavaScript per countdown e tracking -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    const countdownElement = document.querySelector('[data-end-date]');
    if (countdownElement) {
        const endDate = new Date(countdownElement.getAttribute('data-end-date')).getTime();
        
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = endDate - now;
            
            if (distance < 0) {
                clearInterval(timer);
                countdownElement.innerHTML = '<div class="col-span-4 text-red-500 font-bold text-center">Contest Scaduto</div>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            countdownElement.querySelector('.days').textContent = days.toString().padStart(2, '0');
            countdownElement.querySelector('.hours').textContent = hours.toString().padStart(2, '0');
            countdownElement.querySelector('.minutes').textContent = minutes.toString().padStart(2, '0');
            countdownElement.querySelector('.seconds').textContent = seconds.toString().padStart(2, '0');
        }, 1000);
    }
});

// Tracking partecipazione
function instacontestTrackParticipation(contestId) {
    <?php if (is_user_logged_in()): ?>
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=instacontest_track_participation&contest_id=' + contestId + '&nonce=<?php echo wp_create_nonce('track_participation'); ?>'
    }).catch(error => console.log('Tracking error:', error));
    <?php endif; ?>
}
</script>

<!-- Bottom Navigation -->
<?php get_template_part('template-parts/bottom-navigation'); ?>

<?php
endwhile;
get_footer(); ?>
