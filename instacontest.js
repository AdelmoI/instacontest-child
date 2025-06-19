/**
 * INSTACONTEST.JS - JavaScript personalizzato
 * File JavaScript per InstaContest Child Theme
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Console log per confermare che il JS si carica
    console.log('🎯 InstaContest JavaScript caricato con successo!');
    
    // ========================================
    // ANIMAZIONI BOTTOM NAVIGATION
    // ========================================
    
    // Animazione di caricamento della bottom nav
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
    
    // ========================================
    // TOUCH ANIMATIONS PER MOBILE
    // ========================================
    
    // Animazione touch per i link della navigation
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
    
    // ========================================
    // COUNTDOWN TIMERS (se presenti)
    // ========================================
    
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
    
    // Notifica toast semplice
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            max-width: 300px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span>${getToastIcon(type)}</span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animazione di entrata
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        
        // Rimozione automatica
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => document.body.removeChild(toast), 300);
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
    // TRACKING PARTECIPAZIONE CONTEST
    // ========================================
    
    // Funzione per tracciare partecipazione contest
    window.instacontestTrackParticipation = function(contestId) {
        // Questa funzione verrà chiamata dai pulsanti di partecipazione
        console.log('🎯 Partecipazione tracciata per contest:', contestId);
        
        // Se c'è AJAX configurato, invia richiesta al server
        if (typeof instacontest_ajax !== 'undefined') {
            fetch(instacontest_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=instacontest_track_participation&contest_id=${contestId}&nonce=${instacontest_ajax.nonce}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Partecipazione registrata! 🎯', 'success');
                }
            })
            .catch(error => {
                console.log('Errore nel tracking:', error);
            });
        }
    };
    
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
        'Timestamp': new Date().toISOString()
    });
};
