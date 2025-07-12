<div>
    <section class="section">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h1 class="section-title">Meus Pedidos</h1>
                <p class="section-subtitle">Acompanhe o status de todas as suas compras.</p>
            </div>

            <div class="max-w-4xl mx-auto">
                @if($orders->isNotEmpty())
                    {{-- CABEÇALHO DA LISTA (VISÍVEL APENAS NO DESKTOP) --}}
                    <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-3 text-xs font-semibold text-text-muted uppercase tracking-wider">
                        <div class="col-span-6">Item</div> {{-- Aumentado para 6 --}}
                        <div class="col-span-2 text-center">Data</div>
                        <div class="col-span-2 text-center">Valor</div>
                        <div class="col-span-2 text-right">Status & Ações</div> {{-- Juntado --}}
                    </div>

                    {{-- LISTA DE PEDIDOS --}}
                    <div class="space-y-4">
                        @foreach ($orders as $order)
                            <div class="bg-bg-secondary border border-border rounded-lg p-4 flex flex-col md:grid md:grid-cols-12 md:items-center md:gap-4 transition-colors hover:bg-bg-tertiary">

                                {{-- Coluna 1: Imagem e Item --}}
                                <div class="md:col-span-6 flex items-center gap-4">
                                    {{-- ========================================================================== --}}
                                    {{-- IMAGEM ADICIONADA AQUI --}}
                                    {{-- ========================================================================== --}}
                                    <a href="{{ route('order.show', $order) }}">
                                        <img src="{{ optional($order->raffle)->getFirstMediaUrl('raffles', 'thumb') ?: (optional($order->product)->getFirstMediaUrl('product_images', 'thumb') ?: 'https://via.placeholder.com/150') }}"
                                             alt="Imagem do item"
                                             class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                    </a>
                                    <div class="flex-grow">
                                        <p class="font-semibold text-text-light truncate" title="{{ optional($order->raffle)->title ?? optional($order->product)->name ?? 'Item não encontrado' }}">
                                            {{ optional($order->raffle)->title ?? optional($order->product)->name ?? 'Item não encontrado' }}
                                        </p>
                                        <p class="text-xs text-text-muted">Pedido #{{ $order->id }}</p>
                                    </div>
                                </div>

                                {{-- Divisor Mobile --}}
                                <hr class="border-border/50 my-3 md:hidden">

                                {{-- Colunas de Detalhes --}}
                                <div class="contents md:col-span-6 md:grid md:grid-cols-6 md:gap-4 md:items-center">
                                    <div class="flex justify-between items-center md:col-span-2 md:justify-center">
                                        <span class="text-sm text-text-muted md:hidden">Data</span>
                                        <span class="font-medium text-text-muted text-sm">{{ $order->created_at->format('d/m/Y') }}</span>
                                    </div>

                                    <div class="flex justify-between items-center mt-2 md:mt-0 md:col-span-2 md:justify-center">
                                        <span class="text-sm text-text-muted md:hidden">Valor</span>
                                        <p class="font-semibold text-primary-light text-base">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                    </div>

                                    {{-- Ações e Status agrupados --}}
                                    <div class="flex justify-between items-center mt-4 md:mt-0 md:col-span-2 md:justify-end md:gap-4">
                                        <x-order-status-badge :status="$order->status" />
                                        <a href="{{ route('order.show', $order) }}" class="cta-secondary">Ver Detalhes</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($orders->hasPages())
                        <div class="mt-6 p-4">
                            {{ $orders->links() }}
                        </div>
                    @endif

                @else
                    {{-- Bloco "Nenhum pedido" --}}
                                        <div class="py-16 text-center text-text-muted bg-bg-secondary rounded-lg">
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
