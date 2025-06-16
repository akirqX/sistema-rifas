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
                                <li>
                                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded-lg text-primary-light bg-primary-dark/20 font-semibold">
                                        <i class="fas fa-home w-6 text-center"></i>
                                        <span class="ml-3">Início</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('my.orders') }}" class="flex items-center p-2 rounded-lg text-text-muted hover:bg-bg-tertiary hover:text-white transition-colors duration-200">
                                        <i class="fas fa-receipt w-6 text-center"></i>
                                        <span class="ml-3">Meus Pedidos</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('my.tickets') }}" class="flex items-center p-2 rounded-lg text-text-muted hover:bg-bg-tertiary hover:text-white transition-colors duration-200">
                                        <i class="fas fa-ticket-alt w-6 text-center"></i>
                                        <span class="ml-3">Minhas Cotas</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('profile') }}" class="flex items-center p-2 rounded-lg text-text-muted hover:bg-bg-tertiary hover:text-white transition-colors duration-200">
                                        <i class="fas fa-user-circle w-6 text-center"></i>
                                        <span class="ml-3">Meu Perfil</span>
                                    </a>
                                </li>

                                {{-- Link para o Painel de Admin, só aparece se o usuário for admin --}}
                                @if(auth()->user()->is_admin)
                                    <li>
                                        <hr class="border-border my-2">
                                        <a href="{{ route('admin.raffles.index') }}" class="flex items-center p-2 rounded-lg text-accent hover:bg-bg-tertiary font-semibold transition-colors duration-200">
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
                        <h1 class="font-heading text-4xl text-white">
                            Bem-vindo, <span class="highlight">{{ auth()->user()->name }}</span>!
                        </h1>
                        <p class="mt-2 text-lg text-text-muted">
                            Esta é a sua central. Fique por dentro das novidades e acesse nossas rifas.
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

                        <hr class="card-divider my-8">

                        <div class="text-center">
                            <h3 class="text-xl font-bold text-white mb-6">Nossa Comunidade</h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                                {{-- Card Discord --}}
                                <a href="#" class="block p-6 rounded-lg transition-transform duration-300 hover:transform hover:-translate-y-1 bg-bg-tertiary border border-border">
                                    <div class="flex items-center justify-center mb-3">
                                        <i class="fab fa-discord text-3xl" style="color: #5865F2;"></i>
                                        <h4 class="ml-4 text-lg font-semibold text-white">Discord Oficial</h4>
                                    </div>
                                    <p class="text-sm text-text-muted">Converse em tempo real, participe de eventos e tire suas dúvidas.</p>
                                </a>
                                {{-- Card WhatsApp --}}
                                <a href="#" class="block p-6 rounded-lg transition-transform duration-300 hover:transform hover:-translate-y-1 bg-bg-tertiary border border-border">
                                    <div class="flex items-center justify-center mb-3">
                                        <i class="fab fa-whatsapp text-3xl" style="color: #25D366;"></i>
                                        <h4 class="ml-4 text-lg font-semibold text-white">Grupo no WhatsApp</h4>
                                    </div>
                                    <p class="text-sm text-text-muted">Receba notificações importantes sobre as rifas e resultados.</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</x-app-layout>
