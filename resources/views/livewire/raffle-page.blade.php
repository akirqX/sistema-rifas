<div>
    <section class="section">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div>
                    <img class="rounded-2xl shadow-lg w-full aspect-video object-cover border border-border" src="{{ $raffle->getFirstMediaUrl('raffles') ?: 'https://via.placeholder.com/800x450.png?text=Rifa' }}" alt="Imagem da {{ $raffle->title }}">
                </div>
                <div class="bg-bg-secondary p-8 rounded-2xl border border-border">
                    <h1 class="font-heading text-3xl text-white mb-2">{{ $raffle->title }}</h1>
                    <p class="text-2xl font-bold text-primary-light mb-4">R$ {{ number_format($raffle->price, 2, ',', '.') }} por cota</p>
                    <p class="text-text-muted leading-relaxed">{{ $raffle->description }}</p>
                </div>
            </div>

            <div class="bg-bg-secondary p-8 rounded-2xl border border-border">
                <div class="bg-bg-tertiary p-4 rounded-lg mb-6 sticky top-24 z-20">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <span class="font-bold text-white">{{ count($selectedTickets) }}</span>
                            <span class="text-text-muted">cota(s) selecionada(s)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-text-muted">Total:</span>
                            <span class="font-bold text-2xl text-primary-light ml-2">R$ {{ number_format(count($selectedTickets) * $raffle->price, 2, ',', '.') }}</span>
                        </div>
                        <button wire:click="reserveTickets" class="cta-primary w-full md:w-auto" @if(empty($selectedTickets)) disabled @endif>
                            Participar com {{ count($selectedTickets) }} cota(s)
                        </button>
                    </div>
                </div>

                <h3 class="font-heading text-2xl text-center text-white mb-4">Selecione seus números da sorte</h3>
                <div class="grid grid-cols-5 sm:grid-cols-10 md:grid-cols-15 lg:grid-cols-20 gap-2">
                    @foreach ($tickets as $ticket)
                        @php
                            $isSelected = in_array($ticket->number, $selectedTickets);
                            $isDisabled = $ticket->status !== 'available';

                            $class = 'bg-bg-tertiary text-text-muted hover:bg-primary-dark/50';
                            if ($isSelected) $class = 'bg-primary-light text-white ring-2 ring-offset-2 ring-offset-bg-secondary ring-primary-light';
                            if ($isDisabled) $class = 'bg-bg-primary text-text-subtle cursor-not-allowed';
                        @endphp

                        <button
                            wire:click="selectTicket({{ $ticket->number }})"
                            @if($isDisabled) disabled @endif
                            class="p-2 rounded-md aspect-square flex items-center justify-center font-mono font-bold text-sm transition-all duration-200 {{ $class }}"
                        >
                            {{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
