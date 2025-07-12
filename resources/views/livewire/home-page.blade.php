<div>
    {{-- Indicador de Carregamento --}}
    <div wire:loading.flex class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-75 z-[9999] justify-center items-center">
        <div class="text-white text-2xl font-semibold">Carregando...</div>
    </div>

    {{-- ========================================================== --}}
    {{-- 1. SEÇÃO HERÓI COM VÍDEO E CONTADOR --}}
    {{-- ========================================================== --}}
    <section class="hero-section-v2 relative flex items-center justify-center text-center text-white overflow-hidden min-h-[70vh] sm:min-h-[80vh] py-20">

{{-- Em resources/views/livewire/home-page.blade.php --}}

<!-- Vídeo de Fundo do YouTube (Versão com API) -->
<div class="hero-video-background">
    {{-- Note que o ID "youtube-background-player" foi adicionado --}}
    <div id="youtube-background-player"></div>
    <div class="absolute inset-0 bg-black opacity-60 z-10"></div>
</div>

        <!-- Conteúdo da Seção Herói -->
        <div class="relative z-10 container mx-auto px-4" x-data="{ visible: false }" x-init="setTimeout(() => { visible = true }, 100)">
            <h1
                class="hero-title text-4xl md:text-6xl font-extrabold tracking-tight leading-tight mb-4 transition-all duration-700"
                :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
            >
                A <span class="text-highlight">Sorte</span> Bate à Sua <span class="text-highlight">Porta</span>
            </h1>
            <p
                class="hero-subtitle text-lg md:text-xl max-w-3xl mx-auto mb-8 transition-all duration-700 delay-200"
                :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
            >
                Participe de nossas rifas com prêmios incríveis de forma 100% online, segura e transparente. O próximo ganhador pode ser você!
            </p>

            <div
                class="transition-all duration-700 delay-300"
                :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
            >
                <a href="{{ route('raffles.showcase') }}" class="btn-prodgio btn-primary">
                    Ver Todas as Rifas
                </a>
            </div>

            {{-- Contagem Regressiva --}}
            @if($nextRaffleToEnd && $nextRaffleToEnd->drawn_at)
                <div
                    class="countdown-wrapper mt-12 transition-all duration-700 delay-500"
                    :class="visible ? 'opacity-100' : 'opacity-0'"
                    x-data="countdown('{{ $nextRaffleToEnd->drawn_at->toIso8601String() }}')"
                    x-init="init()"
                >
                    <p class="mb-2 text-sm uppercase tracking-widest">{{ $nextRaffleToEnd->title }} encerra em:</p>
                    <div class="flex justify-center items-center gap-4 text-2xl md:text-4xl font-mono">
                        <div><span x-text="days"></span><span class="block text-xs">Dias</span></div>
                        <div>:</div>
                        <div><span x-text="hours"></span><span class="block text-xs">Horas</span></div>
                        <div>:</div>
                        <div><span x-text="minutes"></span><span class="block text-xs">Minutos</span></div>
                        <div>:</div>
                        <div><span x-text="seconds"></span><span class="block text-xs">Segundos</span></div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- 2. RIFAS EM DESTAQUE COM CARROSSEL --}}
    <section class="featured-raffles py-16 sm:py-20 bg-bg-secondary">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="section-title text-3xl md:text-4xl">Rifas em Destaque</h2>
                <p class="section-subtitle mt-2">As melhores oportunidades estão aqui. Não perca tempo!</p>
            </div>

            @if($featuredRaffles && $featuredRaffles->count() > 0)
                <div wire:ignore>
                    <div class="splide">
                        <div class="splide__track">
                            <ul class="splide__list">
                                @foreach($featuredRaffles as $raffle)
                                    <li class="splide__slide">
                                        <div class="raffle-card flex flex-col h-full">
                                            <a href="{{ route('raffle.show', $raffle) }}" class="block overflow-hidden">
                                                <img src="{{ $raffle->cover_image_url }}" alt="Prêmio: {{ $raffle->title }}" class="w-full h-56 object-cover transition-transform duration-300 hover:scale-110">
                                            </a>
                                            <div class="p-6 flex-grow flex flex-col">
                                                <h3 class="raffle-title text-xl font-bold mb-2 flex-grow">
                                                    <a href="{{ route('raffle.show', $raffle) }}" class="hover:text-primary-purple transition-colors">{{ $raffle->title }}</a>
                                                </h3>
                                                <p class="raffle-price text-2xl font-bold mb-4">
                                                    R$ {{ number_format($raffle->ticket_price, 2, ',', '.') }}
                                                    <span class="text-sm font-normal text-gray-400">por cota</span>
                                                </p>
                                                <div class="progress-bar-wrapper mb-4">
                                                    <div class="flex justify-between mb-1 text-sm font-medium">
                                                        <span>{{ $raffle->tickets_sold_count }} / {{ $raffle->total_tickets }} vendidos</span>
                                                        <span>{{ number_format($raffle->progress_percentage, 0) }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-700 rounded-full h-2.5">
                                                        <div class="bg-primary-purple h-2.5 rounded-full" style="width: {{ $raffle->progress_percentage }}%"></div>
                                                    </div>
                                                </div>
                                                <a href="{{ route('raffle.show', $raffle) }}" class="btn-prodgio btn-primary mt-auto w-full text-center">
                                                    Quero Participar
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12 px-6 bg-panel-dark rounded-lg border border-border-subtle">
                    <p class="text-lg">Nenhuma rifa em destaque no momento. Fique de olho, estamos preparando novidades incríveis!</p>
                </div>
            @endif
        </div>
    </section>

    {{-- SEÇÃO: CHAMADA PARA O ARSENAL (SKINS) --}}
    <section class="skins-cta-section py-20">
        <div class="container mx-auto px-4">
            <div class="bg-panel-dark border border-border-subtle rounded-lg p-10 flex flex-col lg:flex-row items-center justify-between gap-10">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white">
                        Explore nosso <span class="text-highlight">Arsenal</span>
                    </h2>
                    <p class="mt-4 text-lg text-text-muted">
                        Cansado de esperar pela sorte? Adquira as melhores skins a pronta entrega. Itens selecionados e inspecionados, prontos para serem seus.
                    </p>
                    <a href="{{ route('skins.index') }}" class="btn-prodgio btn-secondary mt-8 inline-block">
                        Ver Skins Disponíveis
                    </a>
                </div>
                <div class="lg:w-1/2 grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                    @forelse($featuredSkins as $skin)
                        <a href="{{ route('skins.show', $skin) }}" class="group block">
                            <img src="{{ $skin->getFirstMediaUrl('product_images', 'default') }}" alt="{{ $skin->name }}"
                                 class="w-full h-auto object-contain p-2 bg-bg-secondary rounded-lg border border-transparent group-hover:border-primary-purple group-hover:scale-110 transition-all duration-300">
                        </a>
                    @empty
                        <div class="col-span-1 sm:col-span-3 text-center text-text-muted py-10">
                            Nosso arsenal está sendo reabastecido. Volte em breve!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- SEÇÃO: COMO FUNCIONA --}}
    <section class="how-it-works py-16 sm:py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="section-title text-3xl md:text-4xl">É Simples, Rápido e Seguro</h2>
                <p class="section-subtitle mt-2">Em apenas 4 passos você já está concorrendo.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 text-center">
                <div class="step-card">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full mx-auto mb-4">
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="step-title text-xl font-semibold mb-2">1. Escolha a Rifa</h3>
                    <p>Navegue pelos prêmios e escolha o que mais te agrada.</p>
                </div>
                <div class="step-card">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full mx-auto mb-4">
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c.51 0 .962-.328 1.09-.828l2.919-9.563a.75.75 0 0 0-1.352-.412L18 3H7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="step-title text-xl font-semibold mb-2">2. Selecione as Cotas</h3>
                    <p>Quanto mais cotas, mais chances de ganhar. Escolha seus números da sorte!</p>
                </div>
                <div class="step-card">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full mx-auto mb-4">
                         <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5a.75.75 0 0 0-.75.75v13.5a.75.75 0 0 0 .75.75h16.5a.75.75 0 0 0 .75-.75V5.25a.75.75 0 0 0-.75-.75H3.75Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.5h3m-3 3h3m-3 3h3M3.75 4.5v15m16.5-15v15" /></svg>
                    </div>
                    <h3 class="step-title text-xl font-semibold mb-2">3. Pague com PIX</h3>
                    <p>Pague de forma instantânea e segura com o QR Code do Mercado Pago.</p>
                </div>
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

    {{-- SEÇÃO: ÚLTIMOS GANHADORES --}}
    <section class="latest-winners py-16 sm:py-20 bg-bg-secondary">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="section-title text-3xl md:text-4xl">Nossos Últimos Ganhadores</h2>
                <p class="section-subtitle mt-2">A prova de que a sorte existe e nós a entregamos!</p>
            </div>
            @if($latestWinners && $latestWinners->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($latestWinners as $winnerRaffle)
                        @if ($winnerRaffle->winnerTicket && $winnerRaffle->winnerTicket->user)
                            <div class="winner-card flex items-center p-6">
                                <img class="h-20 w-20 rounded-full object-cover mr-6" src="{{ $winnerRaffle->winnerTicket->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($winnerRaffle->winnerTicket->user->name) . '&background=a78bfa&color=fff' }}" alt="Foto de {{ $winnerRaffle->winnerTicket->user->name }}">
                                <div>
                                    <h4 class="winner-name text-xl font-bold">{{ $winnerRaffle->winnerTicket->user->name }}</h4>
                                    <p class="winner-prize mt-1">
                                        Ganhou: <span class="font-semibold">{{ $winnerRaffle->title }}</span>
                                    </p>
                                    <a href="{{ route('raffle.show', $winnerRaffle) }}" class="text-sm mt-2 inline-block hover:text-primary-purple transition-colors">Ver rifa finalizada →</a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 px-6 bg-panel-dark rounded-lg border border-border-subtle">
                    <p class="text-lg">Nossos primeiros sorteios estão a caminho! Seja um dos nossos primeiros ganhadores.</p>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('countdown', (expiry) => ({
                expiry: new Date(expiry),
                days: '00',
                hours: '00',
                minutes: '00',
                seconds: '00',
                init() {
                    this.update();
                    const timer = setInterval(() => {
                        let diff = this.expiry.getTime() - new Date().getTime();
                        if (diff < 0) {
                            clearInterval(timer);
                            this.days = '00'; this.hours = '00'; this.minutes = '00'; this.seconds = '00';
                            return;
                        }
                        this.update();
                    }, 1000);
                },
                update() {
                    let diff = this.expiry.getTime() - new Date().getTime();
                    if (diff < 0) diff = 0;

                    this.seconds = String(Math.floor(diff / 1000) % 60).padStart(2, '0');
                    this.minutes = String(Math.floor(diff / 60000) % 60).padStart(2, '0');
                    this.hours = String(Math.floor(diff / 3600000) % 24).padStart(2, '0');
                    this.days = String(Math.floor(diff / 86400000)).padStart(2, '0');
                }
            }))
        })
    </script>
    @endpush
</div>

{{-- Coloque no final do arquivo resources/views/livewire/home-page.blade.php --}}
@push('scripts')
<script>
    // 1. Carrega a API do IFrame Player do YouTube de forma assíncrona.
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    var player;

    // 2. Esta função é chamada automaticamente pela API quando o código é carregado.
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('youtube-background-player', {
            // Use o ID do seu vídeo aqui. Este que sugeri funciona.
            videoId: 'ugzU46dz6pk',
            playerVars: {
                'autoplay': 1,       // Tocar automaticamente
                'controls': 0,       // Esconder controles
                'rel': 0,            // Não mostrar vídeos relacionados no final
                'showinfo': 0,       // Esconder título do vídeo, etc.
                'mute': 1,           // Começar mudo (obrigatório para autoplay)
                'loop': 1,           // Ativar loop
                'playlist': 'ugzU46dz6pk' // Necessário para o loop funcionar, repita o videoId
            },
            events: {
                // Quando o player estiver pronto, ele toca o vídeo
                'onReady': function(event) {
                    event.target.playVideo();
                },
                // Quando o vídeo acabar, ele começa de novo (garantia extra de loop)
                'onStateChange': function(event) {
                    if (event.data === YT.PlayerState.ENDED) {
                        player.playVideo();
                    }
                }
            }
        });
    }
</script>
@endpush
