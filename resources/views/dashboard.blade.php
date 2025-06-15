<x-app-layout>
    {{-- A estrutura do seu novo layout app.blade.php já será aplicada aqui --}}

    <main>
        {{-- Hero Section Simples de Boas-Vindas --}}
        <section class="section-sm" style="padding-top: var(--spacing-4xl); padding-bottom: var(--spacing-4xl);">
            <div class="container text-center">
                <h1 class="hero-title">
                    Bem-vindo, <span class="highlight">{{ auth()->user()->name }}</span>!
                </h1>
                <p class="hero-subtitle">
                    Esta é a sua central. Fique por dentro das novidades e acesse nossas rifas.
                </p>
            </div>
        </section>

        {{-- Seção de Links e Ações --}}
        <section class="section">
            <div class="container">
                <div class="max-w-4xl mx-auto">
                    {{-- Central de Ações --}}
                    <div class="contact-info-wrapper" style="margin-bottom: var(--spacing-xl);">
                        {{-- Botão Principal para as Rifas --}}
                        <div class="text-center p-6">
                            <p class="text-lg text-gray-800 dark:text-gray-200" style="color: var(--color-text-light);">Pronto para a sorte grande?</p>
                            <a href="{{ route('raffles.showcase') }}" class="cta-primary inline-block mt-4">
                                Ver Rifas Ativas
                            </a>
                        </div>

                        <hr class="card-divider">

                        {{-- Links da Comunidade --}}
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-center mb-6" style="color: var(--color-text-light);">Nossa Comunidade</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Card Discord --}}
                                <a href="#" class="block p-6 rounded-lg transition-transform duration-300 hover:transform hover:-translate-y-1" style="background-color: var(--color-bg-tertiary); border: 1px solid var(--color-border);">
                                    <div class="flex items-center mb-3">
                                        <i class="fab fa-discord text-3xl" style="color: #5865F2;"></i>
                                        <h4 class="ml-4 text-lg font-semibold" style="color: var(--color-text-light);">Discord Oficial</h4>
                                    </div>
                                    <p class="text-sm" style="color: var(--color-text-muted);">Converse em tempo real, participe de eventos e tire suas dúvidas.</p>
                                </a>
                                {{-- Card WhatsApp --}}
                                <a href="#" class="block p-6 rounded-lg transition-transform duration-300 hover:transform hover:-translate-y-1" style="background-color: var(--color-bg-tertiary); border: 1px solid var(--color-border);">
                                    <div class="flex items-center mb-3">
                                        <i class="fab fa-whatsapp text-3xl" style="color: #25D366;"></i>
                                        <h4 class="ml-4 text-lg font-semibold" style="color: var(--color-text-light);">Grupo no WhatsApp</h4>
                                    </div>
                                    <p class="text-sm" style="color: var(--color-text-muted);">Receba notificações importantes sobre as rifas e resultados.</p>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Links Rápidos de Navegação --}}
                    <div class="text-center mt-8">
                        <a href="{{ route('my.orders') }}" class="text-indigo-400 hover:text-indigo-300 mx-4">Meus Pedidos</a>
                        <span class="text-gray-500">|</span>
                        <a href="{{ route('my.tickets') }}" class="text-indigo-400 hover:text-indigo-300 mx-4">Minhas Cotas</a>
                        <span class="text-gray-500">|</span>
                        <a href="{{ route('profile') }}" class="text-indigo-400 hover:text-indigo-300 mx-4">Meu Perfil</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-app-layout>
