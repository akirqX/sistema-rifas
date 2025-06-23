<div>
    <section class="section">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h1 class="section-title">Rifas Disponíveis</h1>
                <p class="section-subtitle">Escolha sua sorte e participe. O próximo vencedor pode ser você!</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse ($raffles as $raffle)
                    <div class="raffle-card group">
                        <a href="{{ route('raffle.show', $raffle) }}" class="block overflow-hidden rounded-t-lg">
                            <img src="{{ $raffle->getFirstMediaUrl('raffles', 'default') ?: 'https://via.placeholder.com/400x300.png?text=Rifa' }}"
                                 alt="Imagem da {{ $raffle->title }}"
                                 class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-300">
                        </a>
                        <div class="p-4 bg-bg-secondary rounded-b-lg border-x border-b border-border">
                            <h3 class="font-bold text-text-light truncate" title="{{ $raffle->title }}">{{ $raffle->title }}</h3>

                            {{-- CORREÇÃO APLICADA AQUI --}}
                            <p class="text-primary-light font-semibold text-lg my-2">R$ {{ number_format($raffle->ticket_price, 2, ',', '.') }} por cota</p>

                            <div class="mt-4">
                                @php
                                    $soldTickets = $raffle->tickets()->where('status', '!=', 'available')->count();
                                    $progress = $raffle->total_tickets > 0 ? ($soldTickets / $raffle->total_tickets) * 100 : 0;
                                @endphp
                                <div class="w-full bg-bg-tertiary rounded-full h-2.5">
                                    <div class="bg-primary-light h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="text-xs text-text-muted mt-1 text-center">{{ $soldTickets }} / {{ $raffle->total_tickets }} cotas vendidas</div>
                            </div>

                            <a href="{{ route('raffle.show', $raffle) }}" class="cta-primary mt-4 w-full justify-center">
                                Participar
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16 bg-bg-secondary rounded-lg border border-border">
                        <h3 class="text-lg text-text-muted">Nenhuma rifa ativa no momento. Volte em breve!</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
