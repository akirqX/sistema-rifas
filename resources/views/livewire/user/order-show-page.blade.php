<div>
    <section class="section">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h1 class="section-title">Detalhes do Pedido</h1>
                <p class="section-subtitle">
                    Pedido #{{ $order->id }} - Realizado em {{ $order->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="max-w-4xl mx-auto">
                {{-- Bloco de Pagamento --}}
                <div class="bg-bg-secondary p-8 rounded-2xl border border-border shadow-2xl mb-8">
                    <h3 class="font-heading text-2xl text-center text-white mb-4">
                        Status: <span class="text-primary-light">{{ ucfirst($order->status) }}</span>
                    </h3>

                    @if($order->status == 'pending' && $pixData)
                        <div class="text-center">
                            <p class="text-text-muted mb-4">Seu pedido expira em: <strong class="text-accent">{{ $order->expires_at->diffForHumans() }}</strong></p>

                            <div class="bg-bg-tertiary p-6 rounded-lg">
                                <h4 class="text-lg font-bold text-white">Pague com Pix para confirmar suas cotas</h4>
                                <p class="text-text-muted mt-2 mb-4">Escaneie o QR Code abaixo com o app do seu banco.</p>

                                {{-- Exibindo o QR Code Real --}}
                                <div class="w-56 h-56 bg-white mx-auto rounded-lg flex items-center justify-center p-2">
                                    <img src="data:image/png;base64,{{ $pixData['qr_code_base64'] }}" alt="QR Code Pix">
                                </div>

                                <p class="text-text-muted mt-6 mb-2">Ou use o código Copia e Cola:</p>

                                {{-- Exibindo o Código Copia e Cola Real com Botão --}}
                                <div class="relative">
                                    <input type="text" readonly value="{{ $pixData['qr_code'] }}" id="pix-code" class="w-full bg-bg-primary border border-border rounded-lg p-3 text-text-muted text-sm pr-10">
                                    <button onclick="copyToClipboard()" class="absolute top-1/2 right-2 -translate-y-1/2 text-text-muted hover:text-white" title="Copiar Código">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($order->status == 'paid')
                        <div class="text-center p-6 bg-green-500/20 rounded-lg border border-green-500/30">
                            <h4 class="text-lg font-bold text-green-300">Pagamento Confirmado!</h4>
                            <p class="text-green-400/80 mt-1">Suas cotas estão garantidas. Boa sorte!</p>
                        </div>
                    @else {{-- Cancelled --}}
                        <div class="text-center p-6 bg-red-500/20 rounded-lg border border-red-500/30">
                            <h4 class="text-lg font-bold text-red-300">Pedido Cancelado</h4>
                            <p class="text-red-400/80 mt-1">O tempo para pagamento deste pedido expirou.</p>
                        </div>
                    @endif
                </div>

                {{-- Resumo do Pedido --}}
                <div class="bg-bg-secondary p-8 rounded-2xl border border-border shadow-2xl">
                    <div class="flex items-center gap-4 pb-6 border-b border-border">
                        <img class="h-20 w-20 object-cover rounded-lg" src="{{ $order->raffle->getFirstMediaUrl('raffles', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Rifa' }}" alt="Imagem da Rifa">
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $order->raffle->title }}</h3>
                            <p class="text-sm text-text-muted">Preço por cota: R$ {{ number_format($order->raffle->price, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="py-6">
                        <h4 class="font-bold text-lg text-white mb-4">Resumo da sua Compra</h4>
                        <div class="flex justify-between items-center text-text-muted">
                            <span>{{ $order->ticket_quantity }} Cota(s)</span>
                            <span>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                        </div>

                        <h4 class="font-bold mt-6 mb-2 text-white">Seus Números:</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($order->tickets as $ticket)
                                <span class="bg-primary-dark text-white font-mono font-bold text-sm py-1 px-3 rounded-full">{{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        function copyToClipboard() {
            const input = document.getElementById('pix-code');
            input.select();
            // Para compatibilidade com dispositivos móveis
            input.setSelectionRange(0, 99999);

            try {
                // Tenta usar a API de Clipboard moderna
                navigator.clipboard.writeText(input.value);
            } catch (err) {
                // Fallback para o método antigo
                document.execCommand('copy');
            }

            // Feedback visual (pode ser trocado por um toast)
            alert('Código PIX copiado!');
        }
    </script>
    @endpush
</div>
