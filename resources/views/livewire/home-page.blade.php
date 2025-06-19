{{--
|--------------------------------------------------------------------------
| Página Inicial (Homepage) - PRODGIO Rifas (Versão Final)
|--------------------------------------------------------------------------
|
| Template revisado para alinhar-se 100% à identidade visual da marca PRODGIO.
| As classes de estilo foram simplificadas para depender do nosso CSS customizado,
| garantindo um design coeso e profissional.
|
--}}

<div>
    {{-- Indicador de Carregamento do Livewire (Mantido) --}}
    <div wire:loading.flex class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-75 z-50 justify-center items-center">
        <div class="text-white text-2xl font-semibold">Carregando...</div>
    </div>

    {{-- ====================================================================== --}}
    {{-- 1. SEÇÃO HERÓI (HERO SECTION) - REVISADA                             --}}
    {{-- ====================================================================== --}}
    <section class="hero-section py-20 sm:py-28">
        <div class="container mx-auto text-center px-4">
            <h1 class="hero-title text-4xl md:text-6xl font-extrabold tracking-tight leading-tight mb-4">
                A <span class="text-highlight">Sorte</span> Bate à Sua <span class="text-highlight">Porta</span>
            </h1>
            <p class="hero-subtitle text-lg md:text-xl max-w-3xl mx-auto mb-8">
                Participe de nossas rifas com prêmios incríveis de forma 100% online, segura e transparente. O próximo ganhador pode ser você!
            </p>
            <a href="{{ route('raffles.showcase') }}" class="btn-prodgio btn-primary">
                Ver Todas as Rifas
            </a>
        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 2. RIFAS EM DESTAQUE - REVISADA                                        --}}
    {{-- ====================================================================== --}}
    <section class="featured-raffles py-16 sm:py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="section-title text-3xl md:text-4xl">Rifas em Destaque</h2>
                <p class="section-subtitle mt-2">As melhores oportunidades estão aqui. Não perca tempo!</p>
            </div>

            @if($featuredRaffles && $featuredRaffles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($featuredRaffles as $raffle)
                        <div class="raffle-card flex flex-col">
                            <a href="{{ route('raffle.show', $raffle) }}">
                                <img src="{{ $raffle->cover_image_url }}" alt="Prêmio: {{ $raffle->title }}" class="w-full h-56 object-cover">
                            </a>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="raffle-title text-xl font-bold mb-2 flex-grow">
                                    <a href="{{ route('raffle.show', $raffle) }}">{{ $raffle->title }}</a>
                                </h3>
                                <p class="raffle-price text-2xl font-bold mb-4">
                                    R$ {{ number_format($raffle->ticket_price, 2, ',', '.') }}
                                    <span class="text-sm font-normal text-gray-400">por cota</span>
                                </p>

                                {{-- Barra de Progresso --}}
                                <div class="progress-bar-wrapper mb-4">
                                    <div class="flex justify-between mb-1 text-sm font-medium">
                                        <span>Progresso</span>
                                        <span>{{ number_format($raffle->progress_percentage, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-700 rounded-full h-2.5">
                                        {{-- A classe 'bg-blue-600' é usada como seletor no CSS, não se preocupe com o nome --}}
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $raffle->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <a href="{{ route('raffle.show', $raffle) }}" class="btn-prodgio btn-primary mt-auto w-full text-center">
                                    Quero Participar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 px-6 bg-gray-900 rounded-lg border border-gray-800">
                    <p class="text-lg">Nenhuma rifa em destaque no momento. Fique de olho, estamos preparando novidades incríveis!</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 3. COMO FUNCIONA - REVISADA                                            --}}
    {{-- ====================================================================== --}}
    <section class="how-it-works py-16 sm:py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="section-title text-3xl md:text-4xl">É Simples, Rápido e Seguro</h2>
                <p class="section-subtitle mt-2">Em apenas 4 passos você já está concorrendo.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 text-center">
                <!-- Passo 1 -->
                <div class="step-card">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full mx-auto mb-4">
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="step-title text-xl font-semibold mb-2">1. Escolha a Rifa</h3>
                    <p>Navegue pelos prêmios e escolha o que mais te agrada.</p>
                </div>
                <!-- Passo 2 -->
                <div class="step-card">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full mx-auto mb-4">
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c.51 0 .962-.328 1.09-.828l2.919-9.563a.75.75 0 0 0-1.352-.412L18 3H7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="step-title text-xl font-semibold mb-2">2. Selecione as Cotas</h3>
                    <p>Quanto mais cotas, mais chances de ganhar. Escolha seus números da sorte!</p>
                </div>
                <!-- Passo 3 -->
                <div class="step-card">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full mx-auto mb-4">
                         <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5a.75.75 0 0 0-.75.75v13.5a.75.75 0 0 0 .75.75h16.5a.75.75 0 0 0 .75-.75V5.25a.75.75 0 0 0-.75-.75H3.75Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.5h3m-3 3h3m-3 3h3M3.75 4.5v15m16.5-15v15" /></svg>
                    </div>
                    <h3 class="step-title text-xl font-semibold mb-2">3. Pague com PIX</h3>
                    <p>Pague de forma instantânea e segura com o QR Code do Mercado Pago.</p>
                </div>
                <!-- Passo 4 -->
                <div class="step-card">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full mx-auto mb-4">
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-6.75c-.621 0-1.125.504-1.125 1.125V18.75M9 15.75h6" /></svg>
                    </div>
                    <h3 class="step-title text-xl font-semibold mb-2">4. Concorra!</h3>
                    <p>Pronto! Suas cotas estão garantidas. Agora é só torcer e acompanhar o sorteio.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 4. ÚLTIMOS GANHADORES (PROVA SOCIAL) - REVISADA                        --}}
    {{-- ====================================================================== --}}
    <section class="latest-winners py-16 sm:py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="section-title text-3xl md:text-4xl">Nossos Últimos Ganhadores</h2>
                <p class="section-subtitle mt-2">A prova de que a sorte existe e nós a entregamos!</p>
            </div>

            @if($latestWinners && $latestWinners->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($latestWinners as $winnerRaffle)
                        @if ($winnerRaffle->winner)
                            <div class="winner-card flex items-center p-6">
                                <img class="h-20 w-20 rounded-full object-cover mr-6" src="{{ $winnerRaffle->winner->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($winnerRaffle->winner->name) . '&background=a78bfa&color=fff' }}" alt="Foto de {{ $winnerRaffle->winner->name }}">
                                <div>
                                    <h4 class="winner-name text-xl font-bold">{{ $winnerRaffle->winner->name }}</h4>
                                    <p class="winner-prize mt-1">
                                        Ganhou: <span class="font-semibold">{{ $winnerRaffle->title }}</span>
                                    </p>
                                    <a href="{{ route('raffle.show', $winnerRaffle) }}" class="text-sm mt-2 inline-block">Ver rifa finalizada →</a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 px-6 bg-gray-900 rounded-lg border border-gray-800">
                    <p class="text-lg">Nossos primeiros sorteios estão a caminho! Seja um dos nossos primeiros ganhadores.</p>
                </div>
            @endif
        </div>
    </section>

</div>
