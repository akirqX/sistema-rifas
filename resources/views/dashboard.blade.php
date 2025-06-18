<x-app-layout>
    {{-- A main tag agora envolve todo o conteúdo da dashboard, incluindo a sidebar --}}
    <main class="pt-24">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row gap-8">

                {{-- =================================== --}}
                {{-- COLUNA 1: SIDEBAR DE NAVEGAÇÃO      --}}
                {{-- =================================== --}}
                <aside class="w-full md:w-64 flex-shrink-0">
                    <div class="bg-bg-secondary p-6 rounded-2xl border border-border sticky top-28">
                        <h3 class="font-heading text-lg text-white mb-4">Meu Painel</h3>
                        <nav>
                            <ul class="space-y-2">
                                {{-- Link Ativo --}}
                                <li>
                                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg font-semibold transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-primary-light bg-primary-dark/20' : 'text-text-muted hover:bg-bg-tertiary hover:text-white' }}">
                                        <i class="fas fa-home w-6 text-center"></i>
                                        <span class="ml-3">Início</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('my.orders') }}" class="flex items-center p-3 rounded-lg font-semibold transition-colors duration-200 {{ request()->routeIs('my.orders*') ? 'text-primary-light bg-primary-dark/20' : 'text-text-muted hover:bg-bg-tertiary hover:text-white' }}">
                                        <i class="fas fa-receipt w-6 text-center"></i>
                                        <span class="ml-3">Meus Pedidos</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('my.tickets') }}" class="flex items-center p-3 rounded-lg font-semibold transition-colors duration-200 {{ request()->routeIs('my.tickets') ? 'text-primary-light bg-primary-dark/20' : 'text-text-muted hover:bg-bg-tertiary hover:text-white' }}">
                                        <i class="fas fa-ticket-alt w-6 text-center"></i>
                                        <span class="ml-3">Minhas Cotas</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-lg font-semibold transition-colors duration-200 {{ request()->routeIs('profile.edit') ? 'text-primary-light bg-primary-dark/20' : 'text-text-muted hover:bg-bg-tertiary hover:text-white' }}">
                                        <i class="fas fa-user-circle w-6 text-center"></i>
                                        <span class="ml-3">Meu Perfil</span>
                                    </a>
                                </li>

                                @if(auth()->user()->is_admin)
                                    <li>
                                        <hr class="border-border my-2">
                                        <a href="{{ route('admin.raffles.index') }}" class="flex items-center p-3 rounded-lg font-semibold transition-colors duration-200 text-accent hover:bg-accent/10">
                                            <i class="fas fa-shield-halved w-6 text-center"></i>
                                            <span class="ml-3">Painel Admin</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </aside>

                {{-- =================================== --}}
                {{-- COLUNA 2: CONTEÚDO PRINCIPAL        --}}
                {{-- =================================== --}}
                <div class="flex-1">
                    {{-- Card de Boas-Vindas --}}
                    <div class="p-8 bg-bg-secondary rounded-2xl border border-border mb-8 text-center">
                        <h2 class="font-heading text-4xl text-white">
                            Bem-vindo, <span class="highlight">{{ auth()->user()->name }}</span>!
                        </h2>
                        <p class="mt-2 text-lg text-text-muted">
                            Esta é a sua central. Use o menu ao lado para navegar.
                        </p>
                    </div>

                    {{-- Card Principal de Ações --}}
                    <div class="p-8 bg-bg-secondary rounded-2xl border border-border">
                        <div class="text-center">
                             <p class="text-xl text-white">Pronto para a sorte grande?</p>
                            <a href="{{ route('raffles.showcase') }}" class="cta-primary inline-block mt-4">
                                Ver Rifas Ativas
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</x-app-layout>
