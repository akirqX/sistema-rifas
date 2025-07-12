<div class="container mx-auto px-4 py-12" @if($order->status === 'pending' && !$hasError) wire:poll.5s="checkPaymentStatus" @endif>
    <div class="max-w-4xl mx-auto">
        {{-- Esta parte inicial não muda --}}
        <div class="text-center mb-8">
            <h1 class="font-heading text-4xl text-primary-light">Detalhes do Pedido</h1>
            <p class="text-text-muted mt-2">Pedido #{{ $order->id }} - Realizado em {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        @if (session('error')) <div class="bg-red-500/10 border border-red-500/30 text-red-300 p-4 rounded-lg text-center mb-6">{{ session('error') }}</div> @endif
        @if (session('success')) <div class="bg-green-500/10 border border-green-500/30 text-green-300 p-4 rounded-lg text-center mb-6">{{ session('success') }}</div> @endif
        @if ($hasError) <div class="bg-red-500/10 border border-red-500/30 text-red-300 p-4 rounded-lg text-center mb-6">{{ $errorMessage }}</div> @endif

        {{-- O Bloco de Pagamento PIX também não muda --}}
        @if ($order->status === 'pending' && !$hasError)
            <div class="bg-bg-secondary rounded-2xl border border-border p-6 md:p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                    {{-- Coluna do QR Code (sem alterações) --}}
                    <div class="text-center flex flex-col items-center border-b md:border-b-0 md:border-r border-border pb-8 md:pb-0 md:pr-8">
                        {{-- ... todo o seu código do qr code, timer e botão ... --}}
                        <h2 class="text-xl font-semibold text-text-light mb-4">Finalize com PIX</h2>
                        @if ($qrCodeBase64)
                            <div class="bg-white p-4 rounded-lg"><img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code Pix" class="w-48 h-48 mx-auto"></div>
                            @if($expiresAt)
                            <div x-data="{
                                    timeLeft: '',
                                    expiry: new Date('{{ $expiresAt->toIso8601String() }}'),
                                    updateTimer() {
                                        const diff = this.expiry.getTime() - new Date().getTime();
                                        if (diff < 0) { this.timeLeft = 'Expirado!'; clearInterval(this.interval); return; }
                                        const minutes = Math.floor(diff / 60000);
                                        const seconds = Math.floor((diff % 60000) / 1000);
                                        this.timeLeft = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                                    },
                                    init() { this.updateTimer(); this.interval = setInterval(() => { this.updateTimer(); }, 1000); }
                                }" x-init="init()" class="mt-4 font-mono">
                                <p class="text-text-muted">Expira em: <span class="text-2xl font-bold" :class="timeLeft === 'Expirado!' ? 'text-red-500' : 'text-primary-light'" x-text="timeLeft"></span></p>
                            </div>
                            @endif
                            <div x-data="{ text: 'Copiar Código PIX' }" class="w-full max-w-xs mt-4"><button @click="navigator.clipboard.writeText('{{ $qrCodeCopyPaste }}'); text = 'Copiado!'; setTimeout(() => { text = 'Copiar Código PIX' }, 2000)" class="w-full font-semibold py-2 px-4 rounded-lg transition-colors duration-200 bg-primary-dark hover:bg-primary-light text-white"><span x-text="text"></span></button></div>
                        @else
                            <div class="w-48 h-48 mx-auto flex items-center justify-center bg-gray-800 rounded-lg"><div wire:loading.flex wire:target="generateMercadoPagoPayment" class="items-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-light"></div></div><div wire:loading.remove wire:target="generateMercadoPagoPayment"><button wire:click="generateMercadoPagoPayment" class="cta-primary">Gerar QR Code</button></div></div>
                        @endif
                    </div>

                    {{-- Coluna do Resumo da Compra (aqui faremos as alterações) --}}
                    <div class="flex flex-col pt-8 md:pt-0 border-t md:border-t-0 md:border-l border-border md:pl-8">
                        <h2 class="text-xl font-semibold text-text-light mb-4">Resumo da Compra</h2>
                        <div class="space-y-4 flex-grow">
                            <div class="flex justify-between items-center"><span class="text-text-muted">Status:</span><x-order-status-badge :status="$order->status" /></div>

                            <!-- ====================================================================== -->
                            <!-- LÓGICA DE EXIBIÇÃO DINÂMICA -->
                            <!-- ====================================================================== -->
                            @if($order->raffle_id && $item)
                                <div class="flex justify-between items-center border-t border-border pt-4"><span class="font-semibold text-white">Item:</span><span class="text-white text-right">{{ $item->title }}</span></div>
                                <div class="flex justify-between items-center"><span class="text-text-muted">Quantidade:</span><span class="text-white">{{ $order->ticket_quantity }} cota(s)</span></div>
                            @elseif($order->product_id && $item)
                                <div class="flex items-start justify-between border-t border-border pt-4">
                                    <span class="font-semibold text-white">Item:</span>
                                    <div class="text-right">
                                        <img src="{{ $item->getFirstMediaUrl('product_images', 'default') }}" alt="{{ $item->name }}" class="w-16 h-16 object-contain p-1 ml-auto mb-2 bg-gray-800 rounded-md">
                                        <span class="text-white">{{ $item->name }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center"><span class="text-text-muted">Quantidade:</span><span class="text-white">1 Unidade</span></div>
                            @endif
                            <!-- ====================================================================== -->

                            <div class="flex justify-between items-center text-xl font-bold border-t border-border pt-4 mt-4"><span class="text-white">Total:</span><span class="text-primary-light">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($order->status === 'paid')
            {{-- Lógica de Pagamento Confirmado (personalizar mensagem se necessário) --}}
            <div class="bg-green-900/50 border border-green-700 text-green-300 p-6 rounded-lg text-center mb-8">
                <p class="font-bold text-lg">🎉 Pagamento Confirmado! 🎉</p>
                @if($order->raffle_id)
                    <p class="mt-2">Obrigado por sua compra. Seus números estão garantidos!</p>
                @else
                    <p class="mt-2">Obrigado por sua compra. Em breve seu produto será entregue.</p>
                @endif
            </div>
        @else
            {{-- Lógica de Pedido Expirado/Falhou (sem alterações) --}}
            <div class="bg-red-900/50 border border-    red-700 text-red-300 p-6 rounded-lg text-center mb-8"><p class="font-bold text-lg">Pedido {{ ucfirst($order->status) }}</p><p class="mt-2">Este pedido não está mais ativo.</p></div>
        @endif

        {{-- Exibe a seção de "Seus Números" apenas se for uma rifa --}}
        @if($order->raffle_id && $order->tickets->isNotEmpty())
            <div class="bg-bg-secondary rounded-2xl border border-border p-6 mt-8">
                <h4 class="font-semibold text-text-light mb-2">Seus Números:</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach ($order->tickets as $ticket)
                        <span class="bg-primary-dark text-white font-mono text-sm font-bold py-1 px-3 rounded-full">{{ $ticket->number }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
