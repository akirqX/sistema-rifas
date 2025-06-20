<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ config('app.name', 'PRODGIO') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Montserrat:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-bg-primary text-text-light">
    <div class="flex h-screen bg-gray-900/50">
        <!-- Menu Lateral (Sidebar) -->
        <aside class="w-64 flex-shrink-0 bg-panel-dark border-r border-border-subtle">
            <div class="p-6">
                <a href="{{ route('admin.dashboard') }}" class="font-heading text-2xl text-white tracking-widest">PRODGIO</a>
                <span class="block text-xs text-primary-light -mt-1">ADMIN</span>
            </div>
            <nav class="mt-8 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700/50 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-dark/30 border-l-4 border-primary-light text-white' : '' }}">
                    <i class="fas fa-tachometer-alt w-6 text-center"></i>
                    <span class="ml-4">Dashboard</span>
                </a>
                <a href="{{ route('home') }}" target="_blank" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-700/50">
                    <i class="fas fa-globe w-6 text-center"></i>
                    <span class="ml-4">Ver Site</span>
                </a>
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-700/50">
                        <i class="fas fa-sign-out-alt w-6 text-center"></i>
                        <span class="ml-4">Sair</span>
                    </a>
                </form>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-bg-primary">
            <div class="container mx-auto px-4 sm:px-6 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
