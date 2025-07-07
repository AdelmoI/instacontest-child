<?php
/**
 * Archive Contest Template - Lista Contest
 * Layout a lista per contest aperti e chiusi
 */

get_header(); ?>

<body class="bg-gray-50">

    <!-- Header -->
    <header id="header" class="fixed top-0 w-full bg-white border-b border-gray-200 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="w-10 h-10 instagram-gradient rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">IC</span>
            </div>
            <h1 class="text-black font-bold text-lg">Tutti i Contest</h1>
            <div class="w-10 h-10"></div> <!-- Spacer -->
        </div>
    </header>

    <!-- Stats Header -->
    <section class="mt-16 px-4 py-6 bg-white">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">🏆</div>
            <h2 class="text-black font-bold text-xl mb-2">Contest InstaContest</h2>
            <p class="text-gray-500">Partecipa e accumula punti per scalare la classifica!</p>
        </div>

        <!-- Quick Stats -->
        <?php 
        $active_contests = instacontest_get_active_contests_new();
        if (empty($active_contests)) {
            $active_contests = instacontest_get_active_contests();
        }
        $ended_contests = instacontest_get_ended_contests();
        $total_active = count($active_contests);
        $total_ended = count($ended_contests);
        ?>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-green-600"><?php echo $total_active; ?></div>
                <div class="text-sm text-gray-600">Aperti</div>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-gray-600"><?php echo $total_ended; ?></div>
                <div class="text-sm text-gray-600">Terminati</div>
            </div>
        </div>
    </section>

    <!-- Contest Aperti -->
    <section id="active-contests" class="px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-black font-bold text-lg flex items-center space-x-2">
                <span>🟢</span>
                <span>Contest Aperti</span>
            </h3>
            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-medium">
                <?php echo $total_active; ?> attivi
            </span>
        </div>

        <div class="space-y-4">
            <?php if ($active_contests): ?>
                <?php foreach ($active_contests as $contest):
                    setup_postdata($contest);
                    $contest_id = $contest->ID;
                    $end_date = get_field('contest_end_date', $contest_id);
                    $prize_name = get_field('prize_name', $contest_id);
                    $prize_value = get_field('prize_value', $contest_id);
                    $prize_image = get_field('prize_image', $contest_id);
                    $instagram_url = get_field('instagram_post_url', $contest_id);
                    $participation_points = get_field('participation_points', $contest_id) ?: 5;
                    
                    // Calcola countdown
                    $end_timestamp = strtotime($end_date);
                    $now = time();
                    $diff = $end_timestamp - $now;
                    $days = floor($diff / 86400);
                    $hours = floor(($diff % 86400) / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                ?>

                <div class="bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start space-x-4">
                        
                        <!-- Immagine Contest -->
                        <div class="flex-shrink-0">
                            <?php if (has_post_thumbnail($contest_id)): ?>
                                <div class="w-20 h-20 rounded-xl overflow-hidden">
                                    <?php echo get_the_post_thumbnail($contest_id, 'thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                                </div>
                            <?php elseif ($prize_image): ?>
                                <img class="w-20 h-20 object-cover rounded-xl" 
                                     src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                     alt="<?php echo esc_attr($prize_name); ?>">
                            <?php else: ?>
                                <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-gift text-purple-500 text-2xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info Contest -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-black font-bold text-lg leading-tight"><?php echo esc_html($prize_name); ?></h4>
                                <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-medium whitespace-nowrap ml-2">
                                    APERTO
                                </span>
                            </div>
                            
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                                <span class="flex items-center space-x-1">
                                    <i class="fa-solid fa-euro-sign"></i>
                                    <span><?php echo number_format($prize_value, 0, ',', '.'); ?></span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <i class="fa-solid fa-coins"></i>
                                    <span>+<?php echo $participation_points; ?> punti</span>
                                </span>
                            </div>

                            <!-- Countdown -->
                            <div class="bg-red-50 border border-red-200 rounded-lg px-3 py-2 mb-4">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock text-red-500"></i>
                                    <span class="text-red-600 font-medium text-sm">
                                        Termina tra <?php echo $days; ?>g <?php echo $hours; ?>h <?php echo $minutes; ?>m
                                    </span>
                                </div>
                            </div>

                            <!-- Azioni -->
                            <div class="flex space-x-3">
                                <a href="<?php echo esc_url($instagram_url); ?>" 
                                   target="_blank"
                                   onclick="instacontestTrackParticipation(<?php echo $contest_id; ?>)"
                                   class="flex-1 btn-participate font-bold py-2 px-4 rounded-lg text-sm text-center flex items-center justify-center space-x-2">
                                    <img src="https://www.instacontest.it/wp-content/uploads/2025/06/instagram-new.png" 
                                         alt="Instagram" class="w-4 h-4">
                                    <span>PARTECIPA</span>
                                </a>
                                
                                <a href="<?php echo get_permalink($contest_id); ?>" 
                                   class="bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm hover:bg-gray-200 transition-colors flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endforeach; wp_reset_postdata(); ?>
            <?php else: ?>
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-calendar-xmark text-gray-300 text-4xl mb-4"></i>
                    <h4 class="text-gray-500 font-medium mb-2">Nessun contest attivo</h4>
                    <p class="text-gray-400 text-sm">I nuovi contest appariranno qui</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contest Terminati -->
    <section id="ended-contests" class="px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-black font-bold text-lg flex items-center space-x-2">
                <span>🔴</span>
                <span>Contest Terminati</span>
            </h3>
            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">
                <?php echo $total_ended; ?> terminati
            </span>
        </div>

        <div class="space-y-4">
            <?php if ($ended_contests): ?>
                <?php 
                $count = 0;
                foreach ($ended_contests as $contest):
                    if ($count >= 10) break; // Max 10 contest terminati
                    setup_postdata($contest);
                    $contest_id = $contest->ID;
                    $prize_name = get_field('prize_name', $contest_id);
                    $prize_value = get_field('prize_value', $contest_id);
                    $prize_image = get_field('prize_image', $contest_id);
                    $winner_points = get_field('winner_points', $contest_id) ?: 50;
                    $status = instacontest_get_contest_status($contest_id);
                    $end_date = get_field('contest_end_date', $contest_id);
                ?>

                <div class="bg-white border border-gray-200 rounded-2xl p-4 opacity-90">
                    <div class="flex items-start space-x-4">
                        
                        <!-- Immagine Contest -->
                        <div class="flex-shrink-0">
                            <?php if (has_post_thumbnail($contest_id)): ?>
                                <div class="w-20 h-20 rounded-xl overflow-hidden grayscale">
                                    <?php echo get_the_post_thumbnail($contest_id, 'thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                                </div>
                            <?php elseif ($prize_image): ?>
                                <img class="w-20 h-20 object-cover rounded-xl grayscale" 
                                     src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                     alt="<?php echo esc_attr($prize_name); ?>">
                            <?php else: ?>
                                <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-gift text-gray-400 text-2xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info Contest -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-gray-700 font-bold text-lg leading-tight"><?php echo esc_html($prize_name); ?></h4>
                                <?php if ($status === 'completed'): ?>
                                    <span class="bg-red-100 text-red-600 px-2 py-1 rounded-lg text-xs font-medium whitespace-nowrap ml-2">
                                        TERMINATO
                                    </span>
                                <?php else: ?>
                                    <span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-lg text-xs font-medium whitespace-nowrap ml-2">
                                        IN SELEZIONE
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                <span class="flex items-center space-x-1">
                                    <i class="fa-solid fa-euro-sign"></i>
                                    <span><?php echo number_format($prize_value, 0, ',', '.'); ?></span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span>Terminato il <?php echo date_i18n('d/m/Y', strtotime($end_date)); ?></span>
                                </span>
                            </div>

                            <!-- Azioni -->
                            <div class="flex space-x-3">
                                <?php if ($status === 'completed'): ?>
                                    <a href="<?php echo get_permalink($contest_id); ?>" 
                                       class="flex-1 btn-discover-winner font-bold py-2 px-4 rounded-lg text-sm text-center flex items-center justify-center space-x-2">
                                        <i class="fa-solid fa-search"></i>
                                        <span>SCOPRI SE HAI VINTO</span>
                                    </a>
                                <?php else: ?>
                                    <div class="flex-1 bg-yellow-100 text-yellow-600 font-medium py-2 px-4 rounded-lg text-sm text-center flex items-center justify-center space-x-2">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                        <span>Vincitore in selezione...</span>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="<?php echo get_permalink($contest_id); ?>" 
                                   class="bg-gray-100 text-gray-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-gray-200 transition-colors flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                    $count++;
                endforeach; 
                wp_reset_postdata(); ?>

                <!-- Link per vedere tutti i contest terminati -->
                <?php if (count($ended_contests) > 10): ?>
                    <div class="text-center pt-4">
                        <button class="text-blue-500 hover:text-blue-600 font-medium text-sm" 
                                onclick="loadMoreEndedContests()">
                            Vedi altri contest terminati (<?php echo count($ended_contests) - 10; ?>)
                        </button>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-history text-gray-300 text-4xl mb-4"></i>
                    <h4 class="text-gray-500 font-medium mb-2">Nessun contest precedente</h4>
                    <p class="text-gray-400 text-sm">I contest terminati appariranno qui</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</body>

<!-- JavaScript per tracking partecipazione -->
<script>
// Tracking partecipazione
function instacontestTrackParticipation(contestId) {
    <?php if (is_user_logged_in()): ?>
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=instacontest_track_participation&contest_id=' + contestId + '&nonce=<?php echo wp_create_nonce('instacontest_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.first_time && data.data.points_awarded > 0) {
            showPointsNotification(data.data.points_awarded, data.data.new_total);
        }
    })
    .catch(error => {
        console.log('Tracking error:', error);
    });
    <?php endif; ?>
}

// Notifica punti
function showPointsNotification(points, totalPoints) {
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300">
            <div class="flex items-center space-x-2">
                <span class="text-2xl">🎯</span>
                <div>
                    <div class="font-bold">+${points} punti!</div>
                    <div class="text-sm opacity-90">Totale: ${totalPoints} punti</div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    const notificationElement = notification.querySelector('div');
    
    setTimeout(() => notificationElement.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        notificationElement.classList.add('translate-x-full');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 4000);
}

// Load more ended contests (opzionale)
function loadMoreEndedContests() {
    // Implementa se necessario
    alert('Funzionalità in arrivo!');
}
</script>

<?php get_footer(); ?>
