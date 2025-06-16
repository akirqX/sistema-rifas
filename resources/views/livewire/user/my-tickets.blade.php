<div>
    <section class="section">
        <div class="container mx-auto px-4">

            {{-- Cabeçalho da Seção --}}
            <div class="section-header">
                <h1 class="section-title">Minhas Cotas</h1>
                <p class="section-subtitle">
                    Aqui estão todos os números que você adquiriu. Boa sorte!
                </p>
            </div>

            {{-- Container para os Cards de Rifa --}}
            <div class="space-y-8">
                {{--
                    O código abaixo agrupa todas as suas cotas pela rifa a que pertencem.
                    Isso cria um bloco para cada rifa.
                --}}
                @forelse ($tickets->groupBy('raffle_id') as $raffleId => $ticketsForRaffle)
                    @php
                        // Pega a informação da rifa a partir da primeira cota do grupo
                        $raffle = $ticketsForRaffle->first()->raffle;
                    @endphp

                    <div class="bg-bg-secondary border border-border rounded-2xl shadow-lg overflow-hidden">
                        {{-- Header do Card da Rifa --}}
                        <div class="p-6 bg-bg-tertiary border-b border-border flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img class="h-16 w-16 object-cover rounded-lg" src="{{ $raffle->getFirstMediaUrl('raffles', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Rifa' }}" alt="Imagem da Rifa">
                                <div>
                                    <h3 class="text-xl font-bold text-text-light">{{ $raffle->title }}</h3>
                                    <p class="text-sm text-text-muted">Status da Rifa: {{ ucfirst($raffle->status) }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-text-muted text-left md:text-right">
                                Você possui <span class="font-bold text-primary-light">{{ $ticketsForRaffle->count() }}</span> cota(s) nesta rifa.
                            </div>
                        </div>

                        {{-- Grid com as Suas Cotas --}}
                        <div class="p-6 grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-12 gap-3">
                            @foreach ($ticketsForRaffle as $ticket)
                                <div class="
                                    flex items-center justify-center
                                    p-2 rounded-md aspect-square
                                    bg-gradient-to-br from-primary-dark to-primary-light
                                    text-white font-bold text-sm md:text-base shadow-md
                                    transition-transform duration-200 hover:scale-110
                                    {{ $raffle->winner_ticket_id == $ticket->id ? 'ring-4 ring-offset-2 ring-offset-bg-secondary ring-accent' : '' }}
                                ">
                                    {{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            @endforeach
                        </div>

                        {{-- Destaque se for o vencedor --}}
                        @if($raffle->winner_ticket_id && $ticketsForRaffle->contains('id', $raffle->winner_ticket_id))
                             <div class="p-4 bg-accent/10 border-t border-accent/30 text-center text-accent font-semibold">
                                Parabéns! Você possui a cota vencedora desta rifa!
                            </div>
                        @endif
                    </div>
                @empty
                    {{-- Mensagem para quando o usuário não tem nenhuma cota --}}
                    <div class="text-center py-16 bg-bg-secondary rounded-lg border border-border">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-text-light">Você ainda não possui cotas</h3>
                        <p class="mt-1 text-sm text-text-muted">Que tal tentar a sorte?</p>
                        <div class="mt-6">
                            <a href="{{ route('raffles.showcase') }}" class="cta-primary">
                                Ver Rifas Ativas
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </section>
</div>
