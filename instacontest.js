/**
 * INSTACONTEST.JS - JavaScript personalizzato
 * File JavaScript per InstaContest Child Theme
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Console log per confermare che il JS si carica
    console.log('🎯 InstaContest JavaScript caricato con successo!');
    
    // ========================================
    // INIZIALIZZAZIONE
    // ========================================
    
    // Inizializza tutte le funzionalità
    initializeBottomNavigation();
    initializeCountdowns();
    hideFormIfResultPresent();
    
    // ========================================
    // ANIMAZIONI BOTTOM NAVIGATION
    // ========================================
    
    function initializeBottomNavigation() {
        const bottomNav = document.getElementById('bottom-nav');
        if (bottomNav) {
            // Animazione smooth di entrata
            bottomNav.style.transform = 'translateY(100%)';
            bottomNav.style.opacity = '0';
            
            setTimeout(() => {
                bottomNav.style.transition = 'all 0.4s ease-out';
                bottomNav.style.transform = 'translateY(0)';
                bottomNav.style.opacity = '1';
            }, 100);
        }
        
        // TOUCH ANIMATIONS PER MOBILE
        const navItems = document.querySelectorAll('#bottom-nav a');
        navItems.forEach(item => {
            item.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.95)';
            });
            
            item.addEventListener('touchend', function() {
                this.style.transform = 'scale(1)';
            });
            
            // Reset al mouse leave per sicurezza
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    }
    
    // ========================================
    // COUNTDOWN TIMERS - VERSIONE MIGLIORATA
    // ========================================
    
    function initializeCountdowns() {
        const countdownElements = document.querySelectorAll('[data-end-date]');
        
        countdownElements.forEach(function(element) {
            const endDate = new Date(element.getAttribute('data-end-date')).getTime();
            
            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = endDate - now;
                
                if (distance < 0) {
                    clearInterval(timer);
                    handleCountdownExpired(element);
                    return;
                }
                
                updateCountdownDisplay(element, distance);
            }, 1000);
            
            // Aggiorna immediatamente
            const now = new Date().getTime();
            const distance = endDate - now;
            if (distance > 0) {
                updateCountdownDisplay(element, distance);
            }
        });
    }
    
    // Aggiorna display countdown
    function updateCountdownDisplay(element, distance) {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Per countdown completo (pagina singola)
        const daysEl = element.querySelector('.days');
        const hoursEl = element.querySelector('.hours');
        const minutesEl = element.querySelector('.minutes');
        const secondsEl = element.querySelector('.seconds');
        
        if (daysEl && hoursEl && minutesEl && secondsEl) {
            daysEl.textContent = String(days).padStart(2, '0');
            hoursEl.textContent = String(hours).padStart(2, '0');
            minutesEl.textContent = String(minutes).padStart(2, '0');
            secondsEl.textContent = String(seconds).padStart(2, '0');
        }
        
        // Per countdown mini (homepage)
        const miniTimer = element.querySelector('.countdown-timer-mini');
        if (miniTimer) {
            let display = '';
            if (days > 0) {
                display = `${days}g ${hours}h`;
            } else if (hours > 0) {
                display = `${hours}h ${minutes}m`;
            } else {
                display = `${minutes}m ${seconds}s`;
            }
            miniTimer.textContent = display;
        }
    }
    
    // Gestisci countdown scaduto
    function handleCountdownExpired(element) {
        const miniTimer = element.querySelector('.countdown-timer-mini');
        if (miniTimer) {
            miniTimer.textContent = 'Scaduto';
            element.classList.add('expired');
        }
        
        // Per countdown completo su pagina singola
        const countdownNumbers = element.querySelectorAll('.countdown-number');
        if (countdownNumbers.length > 0) {
            element.innerHTML = `
                <div class="col-span-4 bg-red-100 border border-red-300 rounded-xl p-4 text-center">
                    <div class="text-red-600 font-bold text-lg">Contest Scaduto</div>
                </div>
            `;
        }
    }
    
    // ========================================
    // GESTIONE FORM VINCITORE
    // ========================================
    
    // Nascondi form se risultato è presente
    function hideFormIfResultPresent() {
        const urlParams = new URLSearchParams(window.location.search);
        const winnerCheck = urlParams.get('winner_check');
        
        if (winnerCheck) {
            const winnerForm = document.querySelector('.winner-form-section');
            if (winnerForm) {
                winnerForm.style.display = 'none';
            }
            
            // Scroll al risultato
            const resultSection = document.querySelector('.winner-result-section');
            if (resultSection) {
                setTimeout(() => {
                    resultSection.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 500);
            }
        }
    }
    
    // ========================================
    // TRACKING PARTECIPAZIONE CONTEST - TAILWIND
    // ========================================
    
    // Funzione per tracciare partecipazione contest con AJAX
    window.instacontestTrackParticipation = function(contestId) {
        console.log('🎯 Partecipazione tracciata per contest:', contestId);
        
        // Solo per utenti loggati
        if (typeof instacontest_ajax === 'undefined') {
            return; // Non loggato, esci
        }
        
        // Mostra feedback immediato
        showParticipationFeedback('⏳ Registrando partecipazione...', 'info');
        
        // Richiesta AJAX
        fetch(instacontest_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'instacontest_track_participation',
                'contest_id': contestId,
                'nonce': instacontest_ajax.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.first_time) {
                    showParticipationFeedback(`🎉 +${data.data.points} punti guadagnati!`, 'success');
                    showToast(`+${data.data.points} punti! 🎯`, 'success');
                    // Aggiorna punti nella UI se presente
                    updateUserPointsDisplay(data.data.points);
                } else {
                    showParticipationFeedback('✅ Già partecipato a questo contest', 'info');
                    showToast('Già partecipato!', 'info');
                }
            }
        })
        .catch(error => {
            console.log('Errore nella registrazione:', error);
            showParticipationFeedback('❌ Errore nella registrazione', 'error');
            showToast('Errore di connessione', 'error');
        });
    };
    
    // ========================================
    // FEEDBACK E ANIMAZIONI - TAILWIND STYLE
    // ========================================
    
    // Mostra feedback partecipazione con Tailwind
    function showParticipationFeedback(message, type = 'info') {
        const feedbackContainer = document.getElementById('participation-feedback');
        if (!feedbackContainer) return;
        
        // Rimuovi feedback esistenti
        feedbackContainer.innerHTML = '';
        
        // Classi Tailwind per ogni tipo
        const typeClasses = {
            'success': 'bg-green-100 border border-green-300 text-green-800',
            'error': 'bg-red-100 border border-red-300 text-red-800',
            'info': 'bg-blue-100 border border-blue-300 text-blue-800',
            'warning': 'bg-yellow-100 border border-yellow-300 text-yellow-800'
        };
        
        const classes = typeClasses[type] || typeClasses['info'];
        
        // Crea nuovo feedback
        feedbackContainer.innerHTML = `
            <div class="${classes} rounded-xl p-4 text-center font-semibold animate-pulse">
                ${message}
            </div>
        `;
        
        // Rimuovi dopo 4 secondi
        setTimeout(() => {
            if (feedbackContainer) {
                feedbackContainer.innerHTML = '';
            }
        }, 4000);
    }
    
    // Aggiorna display punti utente
    function updateUserPointsDisplay(pointsEarned) {
        const userPointsElements = document.querySelectorAll('.user-points, .points-number, .stat-number');
        
        userPointsElements.forEach(element => {
            const currentPoints = parseInt(element.textContent) || 0;
            const newPoints = currentPoints + pointsEarned;
            
            // Animazione contatore
            animateCounter(element, currentPoints, newPoints);
        });
    }
    
    // Animazione contatore punti
    function animateCounter(element, start, end) {
        const duration = 1000; // 1 secondo
        const startTime = performance.now();
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.round(start + (end - start) * progress);
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }
        
        requestAnimationFrame(updateCounter);
    }
    
    // ========================================
    // TOAST NOTIFICATIONS - TAILWIND STYLE
    // ========================================
    
    // Notifica toast con Tailwind
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        
        // Classi base Tailwind
        const baseClasses = 'fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full';
        
        // Classi per tipo
        const typeClasses = {
            'success': 'bg-green-500 text-white',
            'error': 'bg-red-500 text-white', 
            'info': 'bg-blue-500 text-white',
            'warning': 'bg-yellow-500 text-white'
        };
        
        const classes = typeClasses[type] || typeClasses['info'];
        
        toast.className = `${baseClasses} ${classes}`;
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <span class="text-lg">${getToastIcon(type)}</span>
                <span class="font-medium">${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animazione di entrata
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        }, 100);
        
        // Rimozione automatica
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    function getToastIcon(type) {
        const icons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        };
        return icons[type] || icons['info'];
    }
    
    // ========================================
    // UTILITY FUNCTIONS
    // ========================================
    
    // Smooth scroll per link interni
    function smoothScrollTo(target) {
        const element = document.querySelector(target);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
    
    // ========================================
    // PERFORMANCE MONITORING
    // ========================================
    
    // Log tempo di caricamento
    window.addEventListener('load', function() {
        const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
        console.log(`📊 Pagina caricata in: ${loadTime}ms`);
    });
    
});

// ========================================
// FUNCTIONS GLOBALI
// ========================================

// Funzione globale per refresh cache
window.instacontestRefresh = function() {
    location.reload(true);
};

// Funzione globale per debug
window.instacontestDebug = function() {
    console.log('🐛 Debug InstaContest:', {
        'User Agent': navigator.userAgent,
        'Screen': `${screen.width}x${screen.height}`,
        'Viewport': `${window.innerWidth}x${window.innerHeight}`,
        'Timestamp': new Date().toISOString(),
        'AJAX Available': typeof instacontest_ajax !== 'undefined'
    });
};
