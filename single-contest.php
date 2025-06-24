<?php
/**
 * Single Contest Template - Versione Migliorata con Tailwind CSS
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

<!-- Container principale con sfondo gradiente -->
<div class="min-h-screen bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800">
    
    <!-- Header con navigazione -->
    <div class="bg-white/10 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-md mx-auto px-4 py-4">
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
                        <span class="instagram-gradient px-3 py-1 rounded-full text-white text-sm font-semibold flex items-center space-x-1">
                            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            <span>Attivo</span>
                        </span>
                    <?php elseif ($status === 'selecting'): ?>
                        <span class="bg-yellow-500 px-3 py-1 rounded-full text-white text-sm font-semibold flex items-center space-x-1">
                            <span class="w-2 h-2 bg-white rounded-full"></span>
                            <span>In selezione</span>
                        </span>
                    <?php else: ?>
                        <span class="bg-green-500 px-3 py-1 rounded-full text-white text-sm font-semibold flex items-center space-x-1">
                            <span class="w-2 h-2 bg-white rounded-full"></span>
                            <span>Completato</span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section del Premio -->
    <div class="max-w-md mx-auto px-4 py-6">
        <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden">
            
            <!-- Immagine del premio -->
            <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200">
                <?php if ($prize_image): ?>
                    <img src="<?php echo esc_url($prize_image['sizes']['large']); ?>" 
                         alt="<?php echo esc_attr($prize_name); ?>"
                         class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="text-8xl opacity-30">🎁</div>
                    </div>
                <?php endif; ?>
                
                <!-- Gradiente overlay bottom -->
                <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/50 to-transparent"></div>
                
                <!-- Info premio sovrapposta -->
                <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                    <h1 class="text-2xl font-bold mb-2 leading-tight">
                        <?php echo esc_html($prize_name); ?>
                    </h1>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm opacity-90">Valore:</span>
                        <span class="text-lg font-semibold">
                            €<?php echo number_format($prize_value, 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenuto principale -->
            <div class="p-6 space-y-6">
                
                <?php if ($status === 'active'): ?>
                    <!-- CONTEST ATTIVO -->
                    
                    <!-- Countdown Timer -->
                    <div class="text-center space-y-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center justify-center space-x-2">
                            <span>⏰</span>
                            <span>Termina il</span>
                        </h2>
                        <div class="text-sm text-gray-600 mb-4">
                            <?php echo instacontest_format_contest_date($end_date); ?>
                        </div>
                        
                        <!-- Grid countdown -->
                        <div id="countdown-<?php echo $contest_id; ?>" 
                             class="grid grid-cols-4 gap-3"
                             data-end-date="<?php echo date('Y-m-d H:i:s', strtotime($end_date)); ?>">
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <div class="text-2xl font-bold text-gray-800 countdown-number days">00</div>
                                <div class="text-xs text-gray-600 font-medium">Giorni</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <div class="text-2xl font-bold text-gray-800 countdown-number hours">00</div>
                                <div class="text-xs text-gray-600 font-medium">Ore</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <div class="text-2xl font-bold text-gray-800 countdown-number minutes">00</div>
                                <div class="text-xs text-gray-600 font-medium">Min</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <div class="text-2xl font-bold text-gray-800 countdown-number seconds">00</div>
                                <div class="text-xs text-gray-600 font-medium">Sec</div>
                            </div>
                        </div>
                    </div>

                    <!-- Come partecipare -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
                            <span>📋</span>
                            <span>Come partecipare</span>
                        </h3>
                        <div class="bg-blue-50 rounded-xl p-4 text-gray-700 leading-relaxed">
                            <?php echo wpautop($instructions); ?>
                        </div>
                        
                        <!-- Punti info per utenti loggati -->
                        <?php if (is_user_logged_in()): ?>
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="text-2xl">🎯</div>
                                    <div>
                                        <div class="text-sm text-gray-600">Partecipando guadagni</div>
                                        <div class="text-lg font-bold text-purple-600">
                                            +<?php echo $participation_points; ?> punti
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pulsante Partecipa -->
                    <div class="space-y-3">
                        <a href="<?php echo esc_url($instagram_url); ?>" 
                           target="_blank" 
                           class="instagram-gradient w-full py-4 rounded-2xl text-white font-semibold text-center flex items-center justify-center space-x-3 hover:scale-105 transition-transform duration-200 shadow-lg"
                           onclick="instacontestTrackParticipation(<?php echo $contest_id; ?>)">
                            <span class="text-xl">📸</span>
                            <span>Partecipa su Instagram</span>
                        </a>
                        <p class="text-center text-sm text-gray-500">
                            Sarai reindirizzato al post Instagram
                        </p>
                    </div>

                <?php elseif ($status === 'selecting'): ?>
                    <!-- SELEZIONE VINCITORE -->
                    <div class="text-center space-y-6 py-8">
                        <div class="text-6xl animate-bounce">🎲</div>
                        <div class="space-y-2">
                            <h2 class="text-xl font-bold text-gray-800">Contest Terminato!</h2>
                            <p class="text-gray-600">Stiamo selezionando il vincitore...</p>
                        </div>
                        
                        <!-- Loading dots animati -->
                        <div class="flex justify-center space-x-1">
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse delay-100"></div>
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse delay-200"></div>
                        </div>
                        
                        <p class="text-sm text-gray-500">
                            Il vincitore verrà annunciato a breve. Torna a controllare!
                        </p>
                    </div>

                <?php else: ?>
                    <!-- CONTEST COMPLETATO -->
                    <div class="space-y-6">
                        
                        <!-- Annuncio vincitore -->
                        <div class="text-center space-y-3">
                            <div class="text-4xl">🏆</div>
                            <h2 class="text-xl font-bold text-gray-800">Contest Terminato!</h2>
                            <p class="text-gray-600">Il vincitore è stato selezionato. Scopri se sei tu!</p>
                        </div>

                        <!-- Form verifica vincitore -->
                        <form method="post" action="" class="space-y-4">
                            <?php wp_nonce_field('instacontest_check_winner', 'instacontest_check_winner_nonce'); ?>
                            <input type="hidden" name="contest_id" value="<?php echo $contest_id; ?>">
                            
                            <h3 class="text-lg font-semibold text-gray-800">Verifica la tua partecipazione</h3>
                            
                            <!-- Nome e Cognome -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                                    <input type="text" id="nome" name="nome" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="cognome" class="block text-sm font-medium text-gray-700 mb-1">Cognome</label>
                                    <input type="text" id="cognome" name="cognome" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            
                            <!-- Telefono -->
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                                <input type="tel" id="telefono" name="telefono" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            
                            <!-- Username Instagram -->
                            <div>
                                <label for="username_ig" class="block text-sm font-medium text-gray-700 mb-1">Username Instagram</label>
                                <input type="text" id="username_ig" name="username_ig" placeholder="@tusername" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            
                            <!-- Pulsante verifica -->
                            <button type="submit" 
                                    class="w-full instagram-gradient py-3 rounded-xl text-white font-semibold flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                                <span>🔍</span>
                                <span>Verifica risultato</span>
                            </button>
                        </form>

                        <!-- Risultato verifica -->
                        <?php if (isset($_GET['winner_check'])): ?>
                            <div class="mt-6">
                                <?php if ($_GET['winner_check'] === 'won'): ?>
                                    <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6 text-center space-y-3">
                                        <div class="text-5xl">🎉</div>
                                        <h2 class="text-2xl font-bold text-green-800">CONGRATULAZIONI!</h2>
                                        <h3 class="text-lg font-semibold text-green-700">HAI VINTO!</h3>
                                        <p class="text-green-600">Verrai contattato presto per la consegna del premio.</p>
                                        <?php if (is_user_logged_in()): ?>
                                            <div class="bg-yellow-100 rounded-xl p-3 flex items-center justify-center space-x-2">
                                                <span class="text-xl">⭐</span>
                                                <span class="text-yellow-800 font-medium">
                                                    +<?php echo $winner_points; ?> punti extra!
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 text-center space-y-3">
                                        <div class="text-4xl">😔</div>
                                        <h2 class="text-xl font-bold text-gray-800">Mi dispiace</h2>
                                        <h3 class="text-lg font-semibold text-gray-700">Non hai vinto questa volta</h3>
                                        <p class="text-gray-600">Continua a partecipare ai nostri contest!</p>
                                        <a href="<?php echo get_post_type_archive_link('contest'); ?>" 
                                           class="inline-block bg-purple-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-purple-700 transition-colors">
                                            Vedi altri concorsi
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

                <!-- Dettagli Premio -->
                <div class="border-t pt-6 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
                        <span>💎</span>
                        <span>Dettagli premio</span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <div class="text-sm text-gray-600">Valore</div>
                            <div class="text-lg font-bold text-gray-800">
                                €<?php echo number_format($prize_value, 0, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <div class="text-sm text-gray-600">Categoria</div>
                            <div class="text-lg font-bold text-gray-800">Tecnologia</div>
                        </div>
                        <?php if (is_user_logged_in()): ?>
                            <div class="bg-purple-50 rounded-xl p-3 text-center">
                                <div class="text-sm text-purple-600">Punti partecipazione</div>
                                <div class="text-lg font-bold text-purple-800">+<?php echo $participation_points; ?></div>
                            </div>
                            <div class="bg-yellow-50 rounded-xl p-3 text-center">
                                <div class="text-sm text-yellow-600">Punti vincita</div>
                                <div class="text-lg font-bold text-yellow-800">+<?php echo $winner_points; ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sezione partecipanti -->
                <?php if ($status !== 'selecting'): ?>
                <div class="border-t pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
                            <span>👥</span>
                            <span>Partecipanti</span>
                        </h3>
                        <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                            <?php echo rand(500, 2500); ?> iscritti
                        </span>
                    </div>
                    
                    <!-- Avatar partecipanti -->
                    <div class="flex items-center space-x-2">
                        <div class="flex -space-x-2">
                            <?php for($i = 0; $i < 4; $i++): ?>
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full border-2 border-white flex items-center justify-center text-white text-xs font-bold">
                                    <?php echo chr(65 + $i); ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">
                            e altri +<?php echo rand(100, 999); ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Spacer per bottom navigation -->
    <div class="h-20"></div>
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
                countdownElement.innerHTML = '<div class="col-span-4 text-red-500 font-bold">Scaduto</div>';
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
