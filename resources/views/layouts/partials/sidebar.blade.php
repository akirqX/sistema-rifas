<!-- Sidebar -->
<div class="flex flex-col w-64 bg-gray-800 text-gray-100">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-gray-900 shadow-md">
        <a href="{{ route('home') }}" class="flex items-center text-white" wire:navigate>
            <x-application-logo class="block h-9 w-auto fill-current" />
            <span class="ml-3 font-bold text-lg">{{ config('app.name', 'Laravel') }}</span>
        </a>
    </div>

    <!-- Links de Navegação -->
    <nav class="flex-1 px-2 py-4 space-y-2">
        <a href="{{ route('raffles.showcase') }}" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('raffles.showcase') || request()->routeIs('raffle.show') ? 'bg-gray-900' : '' }}" wire:navigate>
            <svg class="h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Vitrine de Rifas
        </a>

        @auth
            <p class="px-4 pt-4 pb-2 text-xs text-gray-400 uppercase tracking-wider">Minha Conta</p>
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-900' : '' }}" wire:navigate>
                <svg class="h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                Meu Painel
            </a>
            <a href="{{ route('my.orders') }}" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('my.orders') ? 'bg-gray-900' : '' }}" wire:navigate>
                <svg class="h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                Meus Pedidos
            </a>
            <a href="{{ route('my.tickets') }}" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('my.tickets') ? 'bg-gray-900' : '' }}" wire:navigate>
                <svg class="h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                Minhas Cotas
            </a>

            @if(auth()->user()->is_admin)
                <p class="px-4 pt-4 pb-2 text-xs text-gray-400 uppercase tracking-wider">Administração</p>
                <a href="{{ route('admin.raffles.index') }}" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('admin.*') ? 'bg-gray-900' : '' }}" wire:navigate>
                    <svg class="h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Gerenciar Rifas
                </a>
            @endif
        @endauth
    </nav>
</div>
