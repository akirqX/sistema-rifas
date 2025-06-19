<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PRODGIO') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Montserrat:wght@400;500;600;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-bg-primary text-text-light">

        <div class="particles-background">
            <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
            <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
        </div>

        <div class="loading-screen"><div class="loading-content"><div class="loading-logo">PRODGIO</div><div class="loading-spinner"><div class="spinner-ring"></div><div class="spinner-ring"></div><div class="spinner-ring"></div></div><div class="loading-text">Carregando...</div></div></div>

        <!-- Botão do Menu Mobile (agora independente) -->
        <button class="navbar-toggle" id="navbar-toggle" aria-label="Abrir/Fechar menu">
            <span class="toggle-line"></span>
            <span class="toggle-line"></span>
            <span class="toggle-line"></span>
        </button>

        <div class="header-banner">🔥 Últimas unidades da Rifa do Mês! Não perca! 🔥</div>

        <header class="header" id="header">
            <nav class="navbar">
                <div class="navbar-container">
                    <a href="{{ route('home') }}" class="navbar-logo"><span class="font-heading text-xl">PRODGIO</span></a>
                    <div class="navbar-menu">
                        <ul class="nav-links">
                            <li class="nav-item"><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Início</a></li>
                            <li class="nav-item"><a href="{{ route('raffles.showcase') }}" class="nav-link {{ request()->routeIs('raffles.showcase') ? 'active' : '' }}">Rifas</a></li>
                        </ul>
                        <div class="navbar-actions">
                            @auth
                                <a href="{{ route('dashboard') }}" class="navbar-cta"><span>Meu Painel</span></a>
                            @else
                                <a href="{{ route('login') }}" class="navbar-cta"><span>Entrar</span></a>
                            @endauth
                        </div>
                                    <div class="navbar-progress">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
                    </div>
                </div>
            </nav>
        </header>

        <div class="navbar-mobile" id="navbar-mobile">
            <div class="mobile-menu">
                <ul class="mobile-links">
                    <li><a href="{{ route('home') }}">Início</a></li>
                    <li><a href="{{ route('raffles.showcase') }}">Rifas</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}">Meu Painel</a></li>
                        <li><a href="{{ route('my.orders') }}">Meus Pedidos</a></li>
                        <li><a href="{{ route('my.tickets') }}">Minhas Cotas</a></li>
                        <li><form method="POST" action="{{ route('logout') }}">@csrf<a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Sair</a></form></li>
                    @else
                        <li><a href="{{ route('login') }}">Entrar</a></li>
                        <li><a href="{{ route('register') }}">Registrar</a></li>
                    @endauth
                </ul>
                <div class="mobile-social">
                    <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <main class="relative z-10">{{ $slot }}</main>

        {{-- FOOTER MELHORADO --}}
        <footer class="footer relative z-10">
            <div class="container mx-auto px-4 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-12 gap-8">
                    <div class="md:col-span-2 lg:col-span-4">
                        <a href="{{ route('home') }}" class="navbar-logo mb-4 inline-block"><span class="font-heading text-2xl uppercase tracking-wider text-white">PRODGIO</span></a>
                        <p class="footer-description">Sua plataforma de rifas e sorteios com segurança e transparência. Transformando sorte em oportunidade.</p>
                        <div class="footer-social"><a href="#" target="_blank" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a><a href="#" target="_blank" class="social-icon" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a></div>
                    </div>
                    <div class="lg:col-span-2"><h4 class="footer-title">Navegação</h4><ul class="footer-links"><li><a href="{{ route('home') }}">Início</a></li><li><a href="{{ route('raffles.showcase') }}">Ver Rifas</a></li><li><a href="{{ route('my.orders') }}">Meus Pedidos</a></li><li><a href="#">Como Funciona</a></li><li><a href="#">Contato</a></li></ul></div>
                    <div class="lg:col-span-2"><h4 class="footer-title">Suporte</h4><ul class="footer-links"><li><a href="#">Termos de Uso</a></li><li><a href="#">Política de Privacidade</a></li><li><a href="#">FAQ</a></li></ul></div>
                    <div class="md:col-span-2 lg:col-span-4"><div class="discord-cta h-full"><div><div class="discord-icon"><i class="fab fa-discord text-2xl"></i></div><h4 class="discord-title">Junte-se à Nossa Comunidade</h4><p class="discord-description">A melhor forma de tirar dúvidas, receber atualizações e conversar com a equipe.</p></div><a href="https://discord.gg/Ydw9XQSWFF" target="_blank" class="discord-button"><span>Entrar no Discord</span></a></div></div>
                </div>
                <div class="footer-bottom mt-12 pt-8"><p class="footer-copyright">© {{ date('Y') }} Prodgio. Todos os direitos reservados.</p></div>
            </div>
        </footer>

        <button class="back-to-top" id="back-to-top" aria-label="Voltar ao topo"><i class="fas fa-arrow-up"></i></button>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
