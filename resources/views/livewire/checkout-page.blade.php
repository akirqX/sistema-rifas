<div>
    <section class="section">
        <div class="container mx-auto px-4">

            {{-- REMOVIDO O @if($isReady) DAQUI --}}

            <div class="section-header">
                <h1 class="section-title">Finalizar Pedido</h1>
                <p class="section-subtitle">Revise suas cotas e prossiga. Seus números estão pré-reservados.</p>
            </div>

            <div class="max-w-2xl mx-auto bg-bg-secondary p-8 rounded-2xl border border-border shadow-2xl">
                {{-- Detalhes da Rifa --}}
                <div class="flex items-center gap-4 pb-6 border-b border-border">
                    {{-- A verificação `isset` previne erros caso $raffle seja nulo --}}
                    @if(isset($raffle))
                        <img class="h-20 w-20 object-cover rounded-lg" src="{{ $raffle->getFirstMediaUrl('raffles', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Rifa' }}" alt="Imagem da Rifa">
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $raffle->title }}</h3>
                            <p class="text-sm text-text-muted">Preço por cota: R$ {{ number_format($raffle->price, 2, ',', '.') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Resumo do Pedido --}}
                <div class="py-6">
                    <h4 class="font-bold text-lg text-white mb-4">Resumo do seu Pedido</h4>
                    <div class="flex justify-between items-center text-text-muted">
                        <span>{{ $ticketCount }} Cota(s) selecionada(s)</span>
                        <span>R$ {{ number_format($totalAmount, 2, ',', '.') }}</span>
                    </div>

                    <h4 class="font-bold mt-6 mb-2 text-white">Seus Números:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($ticketNumbers as $number)
                            <span class="bg-primary-dark text-white font-mono font-bold text-sm py-1 px-3 rounded-full">{{ str_pad($number, 4, '0', STR_PAD_LEFT) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 border-t border-border pt-6">
                    <button wire:click="createOrder" wire:loading.attr="disabled" class="cta-primary w-full justify-center text-lg">
                        <span wire:loading.remove>
                            Criar Pedido e Reservar Cotas
                        </span>
                        <span wire:loading>
                            Processando...
                        </span>
                    </button>
                </div>
            </div>

            {{-- REMOVIDO O @endif DAQUI --}}
        </div>
    </section>
</div>
