<div>
    <section class="section">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h1 class="section-title">Meus Pedidos</h1>
                <p class="section-subtitle">Acompanhe o status de todas as suas compras.</p>
            </div>

            <div class="max-w-4xl mx-auto">
                @if($orders->isNotEmpty())
                    {{-- UMA LISTA SIMPLES E ROBUSTA DE PEDIDOS --}}
                    <div class="space-y-4">
                        @foreach ($orders as $order)
                            {{--
                                LAYOUT FLEXBOX:
                                - No mobile, é um card (flex-col).
                                - No desktop (md:), vira uma linha (flex-row).
                                Isso elimina a necessidade de duas lógicas separadas e da problemática <table>.
                            --}}
                            <div class="bg-bg-secondary border border-border rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                {{-- Informações do Item (Sempre visível) --}}
                                <div class="flex-grow">
                                    <span class="font-bold text-text-light block">{{ optional($order->raffle)->title ?? optional($order->product)->name ?? 'Item não encontrado' }}</span>
                                    <span class="text-xs text-text-muted">Pedido #{{ $order->id }} • {{ $order->created_at->format('d/m/Y') }}</span>
                                </div>

                                {{-- Detalhes Adicionais --}}
                                <div class="flex items-center justify-between w-full md:w-auto md:gap-8 border-t border-border pt-4 md:border-none md:pt-0">
                                    {{-- Valor Total --}}
                                    <div class="text-center">
                                        <span class="text-sm text-text-muted md:hidden">Total</span>
                                        <p class="font-semibold text-primary-light text-lg">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                    </div>

                                    {{-- Status --}}
                                    <div class="text-center">
                                        <span class="text-sm text-text-muted md:hidden">Status</span>
                                        <div><x-order-status-badge :status="$order->status" /></div>
                                    </div>

                                    {{-- Ação --}}
                                    <div class="text-center">
                                         <a href="{{ route('my.orders.show', $order) }}" class="cta-secondary text-sm py-2 px-4">Ver / Pagar</a>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{-- Paginação --}}
                    @if($orders->hasPages())
                        <div class="mt-6 p-4">
                            {{ $orders->links() }}
                        </div>
                    @endif

                @else
                    {{-- Bloco "Nenhum pedido" --}}
                    <div class="py-16 text-center text-text-muted">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-text-subtle mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <p class="text-lg font-medium text-text-light mb-2">Nenhum pedido encontrado</p>
                            <p class="text-text-muted mb-4">Você ainda não fez nenhum pedido.</p>
                            <a href="{{ route('raffles.showcase') }}" class="cta-primary">Explorar Rifas</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
