<?php
/**
 * Template Name: Contest Homepage
 * Pagina contest modificabile da WordPress Admin con stile uguale
 */

get_header(); ?>

<body class="bg-gray-50">

    <!-- Header -->
    <header id="header" class="fixed top-0 w-full bg-white border-b border-gray-200 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="w-10 h-10 instagram-gradient rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">IC</span>
            </div>
            <div></div>
            <i class="fa-solid fa-bars text-black text-xl"></i>
        </div>
    </header>

    <!-- CONTENUTO MODIFICABILE DA WORDPRESS ADMIN -->
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <!-- Sezione Personalizzabile con Immagine in Evidenza -->
        <?php if (has_post_thumbnail() || get_the_content()): ?>
        <section id="custom-content" class="mt-16 px-4 py-6 bg-white">
            <div class="text-center">
                <!-- Immagine in evidenza (se caricata) -->
                <?php if (has_post_thumbnail()): ?>
                    <div class="mb-6">
                        <?php the_post_thumbnail('large', array('class' => 'w-full max-w-md mx-auto rounded-2xl shadow-lg')); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Titolo e contenuto modificabile -->
                <?php if (get_the_title()): ?>
                    <h1 class="text-black font-bold text-2xl mb-4">
                        <?php the_title(); ?>
                    </h1>
                <?php endif; ?>
                
                <!-- Contenuto dell'editor WordPress -->
                <?php if (get_the_content()): ?>
                    <div class="text-gray-600 text-lg leading-relaxed max-w-2xl mx-auto">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>
        
    <?php endwhile; endif; ?>

    <!-- Profile Section -->
    <section id="profile" class="px-4 py-6 bg-white">
        <div class="flex items-center gap-4">
            <?php if (is_user_logged_in()): ?>
                <?php $current_user = wp_get_current_user(); ?>
                <div class="p-1 avatar-gradient rounded-full">
                    <?php echo get_avatar($current_user->ID, 64, '', '', array('class' => 'w-16 h-16 rounded-full border-2 border-white')); ?>
                </div>
                <div>
                    <h2 class="text-black font-bold text-lg">CIAO <?php echo strtoupper($current_user->display_name); ?></h2>
                    <p class="text-gray-400 text-sm">Benvenuto su Instacontest!</p>
                </div>
            <?php else: ?>
                <div class="p-1 avatar-gradient rounded-full">
                    <div class="w-16 h-16 rounded-full border-2 border-white bg-gray-300 flex items-center justify-center">
                        <i class="fa-solid fa-user text-white text-2xl"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-black font-bold text-lg">CIAO OSPITE</h2>
                    <p class="text-gray-400 text-sm"><a href="<?php echo wp_login_url(); ?>" class="text-blue-500">Accedi</a> per partecipare!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Open Contests Section -->
    <section id="open-contests" class="px-4 py-6 bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-black font-medium text-lg">Concorsi aperti</h3>
            <span class="text-blue-500 text-sm">Guarda tutti</span>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-2">
            <?php 
            // Usa la nuova funzione per contest attivi
            $active_contests = instacontest_get_active_contests_new();
            if (empty($active_contests)) {
                // Fallback alla funzione vecchia se non ci sono contest con start_date
                $active_contests = instacontest_get_active_contests();
            }
            
            if ($active_contests): 
                foreach ($active_contests as $contest):
                    setup_postdata($contest);
                    $contest_id = $contest->ID;
                    $end_date = get_field('contest_end_date', $contest_id);
                    $prize_name = get_field('prize_name', $contest_id);
                    $prize_value = get_field('prize_value', $contest_id);
                    $prize_image = get_field('prize_image', $contest_id);
                    $participation_points = get_field('participation_points', $contest_id) ?: 5;
                    
                    // Calcola countdown
                    $end_timestamp = strtotime($end_date);
                    $now = time();
                    $diff = $end_timestamp - $now;
                    $days = floor($diff / 86400);
                    $hours = floor(($diff % 86400) / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    ?>
                    
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 min-w-80 relative">
                        <div class="absolute top-4 right-4">
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">APERTO</span>
                        </div>
                        
                        <!-- Immagine in evidenza come immagine principale -->
                        <?php if (has_post_thumbnail($contest_id)): ?>
                            <div class="w-full h-40 rounded-xl mb-4 overflow-hidden">
                                <?php echo get_the_post_thumbnail($contest_id, 'medium', array('class' => 'w-full h-full object-cover')); ?>
                            </div>
                        <?php else: ?>
                            <div class="w-full h-40 bg-gray-200 rounded-xl mb-4 flex items-center justify-center">
                                <i class="fa-solid fa-gift text-gray-400 text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Titolo e immagine premio piccola -->
                        <div class="flex items-center gap-3 mb-3">
                            <?php if ($prize_image): ?>
                                <img class="w-12 h-12 object-cover rounded-lg border-2 border-gray-200" 
                                     src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                     alt="<?php echo esc_attr($prize_name); ?>">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-gray-100 rounded-lg border-2 border-gray-200 flex items-center justify-center">
                                    <i class="fa-solid fa-gift text-gray-400 text-sm"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h4 class="text-black font-bold text-sm leading-tight"><?php echo esc_html($prize_name); ?></h4>
                                <p class="text-gray-500 text-xs">€<?php echo number_format($prize_value, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 rounded-lg px-3 py-2 mb-4">
                            <span class="text-red-500 text-sm font-medium">
                                Termina tra <?php echo $days; ?>g <?php echo $hours; ?>h <?php echo $minutes; ?>m
                            </span>
                        </div>
                        
                        <a href="<?php echo get_permalink($contest_id); ?>"
                           class="block w-full btn-participate font-bold py-3 rounded-xl text-sm text-center">
                           PARTECIPA ORA
                        </a>

                    </div>
                    
                <?php endforeach; 
                wp_reset_postdata();
            else: ?>
                <div class="bg-white border border-gray-200 rounded-2xl p-8 min-w-80 text-center">
                    <i class="fa-solid fa-trophy text-gray-300 text-4xl mb-4"></i>
                    <h4 class="text-gray-500 font-medium mb-2">Nessun concorso attivo</h4>
                    <p class="text-gray-400 text-sm">Torna presto per nuove opportunità!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Coming Soon Section -->
    <section id="coming-soon" class="px-4 py-6">
        <h3 class="text-black font-medium text-lg mb-4">Prossimamente</h3>
        <div class="space-y-4">
            <?php 
            // Usa la nuova funzione per contest in arrivo
            $coming_contests = instacontest_get_coming_contests();
            
            if ($coming_contests): 
                foreach ($coming_contests as $contest):
                    setup_postdata($contest);
                    $contest_id = $contest->ID;
                    $prize_name = get_field('prize_name', $contest_id);
                    $prize_image = get_field('prize_image', $contest_id);
                    $start_date = get_field('contest_start_date', $contest_id);
                    
                    // Calcola countdown al START del contest
                    $start_timestamp = strtotime($start_date);
                    $now = time();
                    $diff = $start_timestamp - $now;
                    $days = floor($diff / 86400);
                    $hours = floor(($diff % 86400) / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    $seconds = floor($diff % 60);
                    ?>
                    
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                        <div class="relative">
                            <!-- Immagine in evidenza come immagine principale -->
                            <?php if (has_post_thumbnail($contest_id)): ?>
                                <div class="w-full h-48 overflow-hidden">
                                    <?php echo get_the_post_thumbnail($contest_id, 'medium', array('class' => 'w-full h-full object-cover')); ?>
                                </div>
                            <?php else: ?>
                                <div class="w-full h-48 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                    <i class="fa-solid fa-clock text-white text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            
                            <!-- Immagine premio piccola nell'overlay -->
                            <?php if ($prize_image): ?>
                                <div class="absolute top-4 right-4">
                                    <img class="w-16 h-16 object-cover rounded-lg border-2 border-white shadow-lg" 
                                         src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                         alt="<?php echo esc_attr($prize_name); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute bottom-4 left-4">
                                <div class="flex gap-2 mb-2">
                                    <span class="bg-gray-800 text-white px-2 py-1 rounded text-sm monospace"><?php echo str_pad($days, 3, '0', STR_PAD_LEFT); ?></span>
                                    <span class="bg-gray-800 text-white px-2 py-1 rounded text-sm monospace"><?php echo str_pad($hours, 2, '0', STR_PAD_LEFT); ?></span>
                                    <span class="bg-gray-800 text-white px-2 py-1 rounded text-sm monospace"><?php echo str_pad($minutes, 2, '0', STR_PAD_LEFT); ?></span>
                                    <span class="bg-gray-800 text-white px-2 py-1 rounded text-sm monospace"><?php echo str_pad($seconds, 2, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <h4 class="text-white font-bold"><?php echo esc_html($prize_name); ?></h4>
                                <p class="text-gray-200 text-sm">Inizia il <?php echo date_i18n('d/m/Y \a\l\l\e H:i', strtotime($start_date)); ?></p>
                            </div>
                        </div>
                    </div>
                    
                <?php endforeach; 
                wp_reset_postdata();
            else: ?>
                <!-- Placeholder se non ci sono contest in arrivo -->
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-clock text-gray-300 text-4xl mb-4"></i>
                    <h4 class="text-gray-500 font-medium mb-2">Nessun contest in arrivo</h4>
                    <p class="text-gray-400 text-sm">I nuovi contest appariranno qui</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Ended Contests Section -->
    <section id="ended-contests" class="px-4 py-6">
        <h3 class="text-black font-medium text-lg mb-4">Contest terminati</h3>
        <div class="space-y-4">
            <?php 
            $ended_contests = instacontest_get_ended_contests();
            if ($ended_contests): 
                $count = 0;
                foreach ($ended_contests as $contest):
                    if ($count >= 5) break; // Max 5 contest
                    setup_postdata($contest);
                    $contest_id = $contest->ID;
                    $prize_name = get_field('prize_name', $contest_id);
                    $prize_image = get_field('prize_image', $contest_id);
                    $status = instacontest_get_contest_status($contest_id);
                
                    // DEBUG: Mostra informazioni contest (SOLO per admin)
                    if (current_user_can('administrator')):
                        $end_date = get_field('contest_end_date', $contest_id);
                        $winner_username = get_field('winner_username', $contest_id);
                        $is_active = instacontest_is_contest_active($contest_id);
                        $has_winner = instacontest_has_winner($contest_id);
                    ?>
                        <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc; font-size: 12px;">
                            <strong>DEBUG Contest ID: <?php echo $contest_id; ?></strong><br>
                            Contest: <?php echo $prize_name; ?><br>
                            End Date: <?php echo $end_date; ?><br>
                            Winner Username: "<?php echo $winner_username; ?>"<br>
                            Is Active: <?php echo $is_active ? 'YES' : 'NO'; ?><br>
                            Has Winner: <?php echo $has_winner ? 'YES' : 'NO'; ?><br>
                            Status: <?php echo $status; ?><br>
                        </div>
                    <?php endif; ?>
                
                <div class="bg-white border border-gray-200 rounded-2xl p-4">
                        <div class="flex items-start gap-4">
                            <!-- Immagine in evidenza come immagine principale -->
                            <?php if (has_post_thumbnail($contest_id)): ?>
                                <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0">
                                    <?php echo get_the_post_thumbnail($contest_id, 'thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                                </div>
                            <?php else: ?>
                                <div class="w-20 h-20 bg-gray-200 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-gift text-gray-400 text-2xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs font-medium">TERMINATO</span>
                                    
                                    <!-- Immagine premio piccola accanto al badge -->
                                    <?php if ($prize_image): ?>
                                        <img class="w-8 h-8 object-cover rounded border border-gray-200" 
                                             src="<?php echo esc_url($prize_image['sizes']['thumbnail']); ?>" 
                                             alt="<?php echo esc_attr($prize_name); ?>">
                                    <?php endif; ?>
                                </div>
                                
                                <h4 class="text-black font-bold mb-1 truncate"><?php echo esc_html($prize_name); ?></h4>
                                <p class="text-gray-500 text-sm mb-3">Terminato il <?php echo get_the_date('d M Y'); ?></p>
                                
                                <?php if ($status === 'completed'): ?>
                                    <a href="<?php echo get_permalink($contest_id); ?>" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm inline-flex items-center gap-2 transition-colors">
                                        <i class="fa-solid fa-search text-xs"></i>
                                        SCOPRI SE HAI VINTO
                                    </a>
                                <?php else: ?>
                                    <span class="bg-gray-200 text-gray-600 font-bold py-2 px-4 rounded-lg text-sm inline-block">
                                        In elaborazione...
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                <?php 
                    $count++;
                endforeach; 
                wp_reset_postdata();
            else: ?>
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-history text-gray-300 text-4xl mb-4"></i>
                    <h4 class="text-gray-500 font-medium mb-2">Nessun contest precedente</h4>
                    <p class="text-gray-400 text-sm">I contest terminati appariranno qui</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Sezione Bottom Personalizzabile (Campi ACF) -->
    <?php 
    $bottom_content = get_field('bottom_section_content');
    $bottom_title = get_field('bottom_section_title');
    if ($bottom_content || $bottom_title): ?>
        <section class="px-4 py-6 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <?php if ($bottom_title): ?>
                    <h2 class="text-black font-bold text-xl mb-4">
                        <?php echo esc_html($bottom_title); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if ($bottom_content): ?>
                    <div class="text-gray-600 leading-relaxed">
                        <?php echo wp_kses_post($bottom_content); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Spacer per bottom nav -->
    <div class="pb-20"></div>

</body>

<!-- Bottom Navigation -->
<?php get_template_part('template-parts/bottom-navigation'); ?>

<?php get_footer(); ?>
