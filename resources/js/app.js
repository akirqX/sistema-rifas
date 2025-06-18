import './bootstrap';

// ========================================================================
// JAVASCRIPT PARA O DESIGN PRODGIO - VERSÃO FINAL E CORRIGIDA
// ========================================================================

// Função principal que configura todos os scripts da interface.
const initializeProdgioScripts = () => {
    const header = document.getElementById('header');
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMobile = document.getElementById('navbar-mobile');
    const progressBar = document.getElementById('progress-bar');
    const backToTopBtn = document.getElementById('back-to-top');
    const loadingScreen = document.querySelector('.loading-screen');

    // Esconde a tela de loading com um pequeno delay.
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
        }, 150);
    }

    // --- CORREÇÃO DA LÓGICA DO MENU MOBILE ---
    // A lógica de abrir/fechar o menu.
    const toggleMenu = () => {
        if (navbarToggle && navbarMobile) {
            navbarToggle.classList.toggle('active');
            navbarMobile.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        }
    };

    // Anexa o evento de clique ao botão do menu.
    // Usamos uma verificação para garantir que o listener não seja duplicado.
    if (navbarToggle && !navbarToggle.dataset.listenerAttached) {
        navbarToggle.addEventListener('click', toggleMenu);
        navbarToggle.dataset.listenerAttached = 'true';
    }

    // --- Lógica do Header, Scroll e Botão Voltar ao Topo ---
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


// --- EXECUÇÃO DOS SCRIPTS ---

// 1. Executa na carga inicial da página.
document.addEventListener('DOMContentLoaded', initializeProdgioScripts);

// 2. Ouve o evento do Livewire e re-executa a função a cada navegação.
document.addEventListener('livewire:navigated', () => {
    initializeProdgioScripts();
});
