import './bootstrap';

// ========================================================================
// 1. IMPORTAÇÕES NECESSÁRIAS NO TOPO
// ========================================================================
import Alpine from 'alpinejs';
import Splide from '@splidejs/splide';
import '@splidejs/splide/css'; // Importa o CSS do carrossel

// ========================================================================
// 2. DISPONIBILIZAÇÃO GLOBAL
// ========================================================================
window.Alpine = Alpine;
window.Splide = Splide; // Torna o Splide acessível em todo o site

// ========================================================================
// 3. SUA FUNÇÃO ORIGINAL (INTACTA)
// ========================================================================
const initializeProdgioScripts = () => {
    const header = document.getElementById('header');
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMobile = document.getElementById('navbar-mobile');
    const progressBar = document.getElementById('progress-bar');
    const backToTopBtn = document.getElementById('back-to-top');
    const loadingScreen = document.querySelector('.loading-screen');

    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
        }, 150);
    }

    const toggleMenu = () => {
        if (navbarToggle && navbarMobile) {
            navbarToggle.classList.toggle('active');
            navbarMobile.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        }
    };

    if (navbarToggle && !navbarToggle.dataset.listenerAttached) {
        navbarToggle.addEventListener('click', toggleMenu);
        navbarToggle.dataset.listenerAttached = 'true';
    }

    const handleScroll = () => {
        if (header && window.scrollY > 50) {
            header.classList.add('scrolled');
        } else if (header) {
            header.classList.remove('scrolled');
        }

        if (progressBar) {
            const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = totalHeight > 0 ? (window.scrollY / totalHeight) * 100 : 0;
            progressBar.style.width = `${progress}%`;
        }

        if (backToTopBtn && window.scrollY > 300) {
            backToTopBtn.classList.add('visible');
        } else if (backToTopBtn) {
            backToTopBtn.classList.remove('visible');
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });

    if (backToTopBtn && !backToTopBtn.dataset.listenerAttached) {
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        backToTopBtn.dataset.listenerAttached = 'true';
    }
};

// ========================================================================
// 4. NOVA FUNÇÃO PARA INICIALIZAR O CARROSSEL
// ========================================================================
const initializeSplideCarousels = () => {
    const splideElements = document.querySelectorAll('.splide');
    splideElements.forEach(element => {
        if (!element.classList.contains('is-initialized')) {
            new Splide(element, {
                type: 'loop',
                perPage: 4,
                perMove: 1,
                gap: '2rem',
                pagination: false,
                arrows: true,
                breakpoints: {
                    1280: { perPage: 3, gap: '1.5rem' },
                    1024: { perPage: 2, gap: '1.5rem' },
                    640: { perPage: 1, gap: '1rem' }
                }
            }).mount();
        }
    });
};


// ========================================================================
// 5. EXECUÇÃO E LISTENERS
// ========================================================================

// Inicia o Alpine.js (ESSENCIAL PARA O LIVEWIRE V3)
Alpine.start();

// Executa na carga inicial da página.
document.addEventListener('DOMContentLoaded', () => {
    initializeProdgioScripts();
    initializeSplideCarousels();
});

// Ouve o evento do Livewire e re-executa as funções a cada navegação.
document.addEventListener('livewire:navigated', () => {
    initializeProdgioScripts();
    initializeSplideCarousels();
});
