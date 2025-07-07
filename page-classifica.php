<?php
/**
 * Template Name: Classifica
 * Pagina classifica con stile identico alla homepage
 */

get_header(); 

// Ottieni dati per la classifica
$top_users = instacontest_get_top_users(10);
$stats = instacontest_get_leaderboard_stats();
$current_user_id = get_current_user_id();
$current_user_position = $current_user_id ? instacontest_get_user_position($current_user_id) : 0;
$current_user_points = $current_user_id ? instacontest_get_user_points($current_user_id) : 0;
?>

<body class="bg-gray-50">

    <!-- Header -->
    <header id="header" class="fixed top-0 w-full bg-white border-b border-gray-200 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="<?php echo home_url(); ?>" class="w-10 h-10 instagram-gradient rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">IC</span>
            </a>
            <h1 class="text-black font-bold text-lg">Classifica</h1>
            <div class="w-10 h-10"></div> <!-- Spacer -->
        </div>
    </header>

    <!-- Statistiche Header -->
    <section class="mt-16 px-4 py-6 bg-white">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">🏆</div>
            <h2 class="text-black font-bold text-xl mb-2">Classifica Generale</h2>
            <p class="text-gray-500">Accumula punti partecipando ai contest!</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-purple-600"><?php echo number_format($stats['total_users']); ?></div>
                <div class="text-sm text-gray-600">Partecipanti</div>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-blue-600"><?php echo number_format($stats['total_points']); ?></div>
                <div class="text-sm text-gray-600">Punti Totali</div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-green-600"><?php echo $stats['avg_points']; ?></div>
                <div class="text-sm text-gray-600">Media Punti</div>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-orange-600"><?php echo $stats['total_contests']; ?></div>
                <div class="text-sm text-gray-600">Contest Totali</div>
            </div>
        </div>
    </section>

    <!-- Schedina Utente Corrente (se loggato) -->
    <?php if (is_user_logged_in()): ?>
    <section class="px-4 pb-4">
        <div id="current-user-card" 
             class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl p-4 text-white cursor-pointer transform transition-transform hover:scale-105"
             onclick="scrollToUserPosition()">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <?php echo get_avatar($current_user_id, 48, '', '', array('class' => 'w-12 h-12 rounded-full border-2 border-white')); ?>
                        <div class="absolute -top-1 -right-1 bg-yellow-400 text-black text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                            #<?php echo $current_user_position; ?>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold">La tua posizione</h3>
                        <p class="text-white/80 text-sm"><?php echo $current_user_points; ?> punti</p>
                    </div>
                </div>
                <div class="text-right">
                    <i class="fa-solid fa-chevron-right text-white/60"></i>
                    <p class="text-xs text-white/80 mt-1">Tocca per vedere</p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Top 10 Classifica -->
    <section class="px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-black font-bold text-lg">🥇 Top 10</h3>
            <span class="text-gray-500 text-sm"><?php echo count($top_users); ?> utenti</span>
        </div>

        <div class="space-y-3" id="top-leaderboard">
            <?php 
            if ($top_users): 
                foreach ($top_users as $user_data):
                    $position = $user_data['position'];
                    $user = (object) array('ID' => $user_data['user_id'], 'user_login' => $user_data['user_login']);
                    $is_current_user = $current_user_id && $user_data['user_id'] == $current_user_id;
                    $user_points = $user_data['total_points'];
                    $participations = $user_data['participations'];
                    $wins = $user_data['wins'];
                    
                    // Include template part
                    include(get_stylesheet_directory() . '/template-parts/leaderboard-item.php');
                endforeach;
            else: ?>
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-trophy text-gray-300 text-4xl mb-4"></i>
                    <h4 class="text-gray-500 font-medium mb-2">Nessun utente in classifica</h4>
                    <p class="text-gray-400 text-sm">Partecipa ai contest per essere il primo!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Classifica Completa -->
    <section class="px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-black font-bold text-lg">📊 Classifica Completa</h3>
            <button id="load-around-user" 
                    class="text-blue-500 text-sm font-medium"
                    <?php echo !$current_user_id ? 'style="display:none;"' : ''; ?>
                    onclick="loadAroundCurrentUser()">
                Vai alla tua posizione
            </button>
        </div>

        <!-- Lista completa con infinite scroll -->
        <div class="space-y-3" id="full-leaderboard">
            <!-- Gli utenti dopo la top 10 verranno caricati qui -->
        </div>

        <!-- Loading indicator -->
        <div id="loading-indicator" class="text-center py-6 hidden">
            <div class="inline-flex items-center space-x-2 text-gray-500">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <span>Caricamento...</span>
            </div>
        </div>

        <!-- Pulsante carica altri -->
        <div class="text-center mt-6">
            <button id="load-more-btn" 
                    class="bg-white border border-gray-300 text-gray-700 font-medium py-3 px-6 rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-chevron-down mr-2"></i>
                Carica altri utenti
            </button>
        </div>
    </section>

    <!-- Modal "Intorno a te" -->
    <div id="around-user-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full max-h-[80vh] overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Intorno alla tua posizione</h3>
                <button onclick="closeAroundUserModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <div id="around-user-content" class="p-4 space-y-3 max-h-96 overflow-y-auto">
                <!-- Contenuto caricato dinamicamente -->
            </div>
        </div>
    </div>

    <!-- Spacer per bottom nav -->
    <div class="pb-20"></div>

</body>

<!-- JavaScript per infinite scroll e modal -->
<script>
let currentPage = 2; // Iniziamo dalla pagina 2 (la 1 è la top 10)
let isLoading = false;
let hasMoreData = true;

document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-load della seconda pagina
    if (hasMoreData) {
        loadMoreUsers();
    }
    
    // Event listener per il pulsante "Carica altri"
    document.getElementById('load-more-btn').addEventListener('click', loadMoreUsers);
    
    // Infinite scroll (opzionale)
    window.addEventListener('scroll', function() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
            if (!isLoading && hasMoreData) {
                loadMoreUsers();
            }
        }
    });
});

function loadMoreUsers() {
    if (isLoading || !hasMoreData) return;
    
    isLoading = true;
    document.getElementById('loading-indicator').classList.remove('hidden');
    document.getElementById('load-more-btn').style.opacity = '0.5';
    
    const formData = new FormData();
    formData.append('action', 'instacontest_load_leaderboard_page');
    formData.append('page', currentPage);
    formData.append('nonce', '<?php echo wp_create_nonce('instacontest_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('full-leaderboard').insertAdjacentHTML('beforeend', data.data.html);
            currentPage++;
            hasMoreData = data.data.has_more;
            
            if (!hasMoreData) {
                document.getElementById('load-more-btn').style.display = 'none';
            }
        } else {
            hasMoreData = false;
            document.getElementById('load-more-btn').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Errore nel caricamento:', error);
        hasMoreData = false;
    })
    .finally(() => {
        isLoading = false;
        document.getElementById('loading-indicator').classList.add('hidden');
        document.getElementById('load-more-btn').style.opacity = '1';
    });
}

function scrollToUserPosition() {
    <?php if ($current_user_id): ?>
        loadAroundCurrentUser();
    <?php endif; ?>
}

function loadAroundCurrentUser() {
    // Mostra modal con utenti intorno alla posizione corrente
    const modal = document.getElementById('around-user-modal');
    const content = document.getElementById('around-user-content');
    
    content.innerHTML = '<div class="text-center py-4"><i class="fa-solid fa-spinner fa-spin text-gray-400"></i></div>';
    modal.classList.remove('hidden');
    
    const formData = new FormData();
    formData.append('action', 'instacontest_load_around_user');
    formData.append('nonce', '<?php echo wp_create_nonce('instacontest_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            content.innerHTML = data.data.html;
        } else {
            content.innerHTML = '<div class="text-center py-4 text-gray-500">Errore nel caricamento</div>';
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        content.innerHTML = '<div class="text-center py-4 text-gray-500">Errore nel caricamento</div>';
    });
}

function closeAroundUserModal() {
    document.getElementById('around-user-modal').classList.add('hidden');
}

// Chiudi modal cliccando fuori
document.getElementById('around-user-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAroundUserModal();
    }
});
</script>


<?php get_footer(); ?>
