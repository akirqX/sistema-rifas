import './bootstrap';

// ========================================================================
// JAVASCRIPT PARA O DESIGN PRODGIO - VERSÃO FINAL E CORRIGIDA
// ========================================================================

// Criamos uma função reutilizável para inicializar todos os scripts da interface.
// Isso é importante porque precisamos rodá-la em toda navegação.
const initializeProdgioScripts = () => {

    const header = document.getElementById('header');
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMobile = document.getElementById('navbar-mobile');
    const progressBar = document.getElementById('progress-bar');
    const backToTopBtn = document.getElementById('back-to-top');
    const loadingScreen = document.querySelector('.loading-screen');

    // --- CORREÇÃO DO LOADING SCREEN ---
    // A lógica para esconder a tela de loading agora está aqui dentro.
    if (loadingScreen) {
        // Um pequeno delay para a animação de fade-out ser visível e suave.
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
        }, 150);
    }

    // --- Lógica do Menu Mobile ---
    if (navbarToggle && navbarMobile) {
        // Removemos eventuais listeners antigos para evitar duplicação
        const newToggle = navbarToggle.cloneNode(true);
        navbarToggle.parentNode.replaceChild(newToggle, navbarToggle);

        newToggle.addEventListener('click', () => {
            newToggle.classList.toggle('active');
            navbarMobile.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        });
    }

    // --- Lógica do Header e Barra de Progresso ---
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

    // Anexamos o evento de scroll uma vez.
    window.addEventListener('scroll', handleScroll, { passive: true });

    // Ação do botão "Voltar ao Topo"
    if (backToTopBtn) {
        const newBtn = backToTopBtn.cloneNode(true);
        backToTopBtn.parentNode.replaceChild(newBtn, backToTopBtn);
        newBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
};


// --- EXECUÇÃO DOS SCRIPTS ---

// 1. Executa os scripts na carga inicial da página.
document.addEventListener('DOMContentLoaded', () => {
    initializeProdgioScripts();
});

// 2. CORREÇÃO CRÍTICA: Ouve o evento do Livewire e executa os scripts NOVAMENTE a cada navegação SPA.
document.addEventListener('livewire:navigated', () => {
    // Esconde o menu mobile caso ele estivesse aberto na página anterior.
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMobile = document.getElementById('navbar-mobile');
    if (navbarToggle && navbarMobile) {
        navbarToggle.classList.remove('active');
        navbarMobile.classList.remove('active');
        document.body.classList.remove('no-scroll');
    }

    // Re-inicializa os scripts para a nova página.
    initializeProdgioScripts();
});
