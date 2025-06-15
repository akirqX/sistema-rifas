<div>
    {{-- A barra de compra inferior só aparece se a rifa NÃO estiver finalizada --}}
    @if ($raffle->status !== 'finished')
        <div class="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 p-4 shadow-lg z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center text-white sm:px-6 lg:px-8">
                <div>
                    <span class="text-sm text-gray-400">Selecionadas</span>
                    <p class="text-2xl font-bold">{{ count($selectedTickets) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-400">Total</span>
                    <p class="text-2xl font-bold text-green-400">R$ {{ number_format(count($selectedTickets) * $raffle->ticket_price, 2, ',', '.') }}</p>
                </div>
                <button wire:click="reserveTickets" wire:loading.attr="disabled" class="bg-green-500 hover:bg-green-600 font-bold py-3 px-8 rounded-lg text-lg disabled:opacity-50 disabled:cursor-wait">
                    <span wire:loading.remove>COMPRAR</span>
                    <span wire:loading>AGUARDE...</span>
                </button>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- O NOVO CARD DO VENCEDOR --}}
            @if ($raffle->status === 'finished' && $raffle->winner)
                <div class="bg-gradient-to-r from-green-400 to-blue-500 text-white p-8 rounded-2xl shadow-2xl mb-8 text-center overflow-hidden relative">
                    <h2 class="text-3xl font-bold tracking-tight">🎉 Rifa Finalizada! 🎉</h2>
                    <p class="mt-2 text-lg opacity-90">Sorteio realizado em: {{ $raffle->drawn_at->format('d/m/Y \à\s H:i') }}</p>

                    <div class="mt-8 flex flex-col items-center">
                        <img class="w-24 h-24 rounded-full border-4 border-white shadow-lg" src="{{ $raffle->winner->user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($raffle->winner->user->name).'&color=7F9CF5&background=EBF4FF' }}" alt="Avatar do Vencedor">

                        <p class="text-2xl font-bold mt-4">
                            {{ $raffle->winner->user->name ?? 'Nome não disponível' }}
                        </p>
                        <p class="text-lg opacity-90">foi o grande vencedor(a)!</p>

                        <div class="mt-6 bg-white bg-opacity-20 rounded-lg p-4">
                            <p class="text-xl">Com a cota de número:</p>
                            <p class="text-7xl font-black tracking-widest my-2">
                                {{ str_pad($raffle->winner->number, 4, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>

                        <div class="mt-6 text-sm opacity-80">
                            @if($raffle->winner->user->getMaskedPhone())
                                <span>Telefone: {{ $raffle->winner->user->getMaskedPhone() }}</span>
                            @endif
                            @if($raffle->winner->user->getMaskedPhone() && $raffle->winner->user->getMaskedCpf())
                                <span class="mx-2">|</span>
                            @endif
                            @if($raffle->winner->user->getMaskedCpf())
                                <span>CPF: {{ $raffle->winner->user->getMaskedCpf() }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- O SCRIPT QUE DISPARA O CONFETE -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        function fireConfetti() {
                            const duration = 5 * 1000;
                            const animationEnd = Date.now() + duration;
                            const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

                            function randomInRange(min, max) {
                                return Math.random() * (max - min) + min;
                            }

                            const interval = setInterval(function() {
                                const timeLeft = animationEnd - Date.now();

                                if (timeLeft <= 0) {
                                    return clearInterval(interval);
                                }

                                const particleCount = 50 * (timeLeft / duration);
                                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
                            }, 250);
                        }
                        fireConfetti();
                    });
                </script>

            @endif

            {{-- O restante da página (grid de cotas) só aparece se a rifa NÃO estiver finalizada --}}
            @if ($raffle->status !== 'finished')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="md:flex">
                            <div class="md:flex-shrink-0">
                                <a href="{{ $raffle->getFirstMediaUrl('raffles') }}" target="_blank">
                                    <img class="rounded-lg md:w-56 aspect-video object-cover" src="{{ $raffle->getFirstMediaUrl('raffles') ?: 'https://via.placeholder.com/1600x900.png?text=Sem+Imagem' }}" alt="Imagem da Rifa">
                                </a>
                            </div>
                            <div class="mt-4 md:mt-0 md:ml-6">
                                <h1 class="text-3xl font-bold mb-2">{{ $raffle->title }}</h1>
                                <p class="text-gray-600 mb-6">{{ $raffle->description }}</p>
                            </div>
                        </div>

                        <hr class="my-6">

                        <h2 class="text-2xl font-bold mb-4 text-center">Selecione suas cotas</h2>
                        <div class="grid grid-cols-5 sm:grid-cols-10 md:grid-cols-15 lg:grid-cols-20 gap-1 text-xs">
                            @foreach ($tickets as $ticket)
                                @php
                                    $isSelected = isset($selectedTickets[$ticket->id]);
                                    $isReserved = $ticket->status === 'reserved';
                                    $isPaid = $ticket->status === 'paid';

                                    $class = 'bg-gray-200 hover:bg-blue-400 text-gray-800'; // Available
                                    if ($isSelected) $class = 'bg-green-500 text-white ring-2 ring-offset-2 ring-green-500';
                                    if ($isReserved) $class = 'bg-yellow-500 text-white cursor-not-allowed';
                                    if ($isPaid) $class = 'bg-red-600 text-white cursor-not-allowed';
                                @endphp
                                <button
                                    @if($ticket->status === 'available')
                                        wire:click="selectTicket({{ $ticket->id }})"
                                    @else
                                        disabled
                                    @endif
                                    class="p-2 rounded-md text-center font-mono font-semibold transition-all duration-150 {{ $class }}"
                                >
                                    {{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
