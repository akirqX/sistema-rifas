<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">

        <div class="text-center mb-8">
            <h1 class="font-heading text-4xl text-primary-light">Detalhes do Pedido</h1>
            <p class="text-text-muted mt-2">Pedido #{{ $order->id }} - Realizado em {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="bg-bg-secondary rounded-2xl border border-border p-6 mb-8">
            <div class="text-center mb-6">
                <span class="text-text-muted">Status:</span>
                <span class="text-2xl font-bold
                    @if($order->status === 'paid') text-green-400 @endif
                    @if($order->status === 'pending') text-yellow-400 @endif
                    @if($order->status === 'expired' || $order->status === 'cancelled') text-red-400 @endif
                ">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            @if ($order->status === 'pending')
                <div wire:poll.10s>
                    @if ($order->expires_at && $order->expires_at->isFuture())
                        <p class="text-center text-text-muted text-sm mb-4">Seu pedido expira em:
                            <span class="font-semibold text-white" x-data="timer('{{ $order->expires_at->toIso8601String() }}')" x-text="time_left"></span>
                        </p>
                    @else
                        <p class="text-center font-semibold text-red-400 text-sm mb-4">Expirado!</p>
                    @endif

                    {{-- A LÓGICA CORRETA PARA LER DA SUA ESTRUTURA --}}
                    @if (isset($order->payment_details['qr_code_base64']))
                        <div class="text-center space-y-4">
                            <p class="text-text-light font-semibold">Pague com Pix para confirmar suas cotas</p>
                            <div class="flex justify-center bg-white p-2 rounded-lg max-w-[256px] mx-auto">
                                <img src="data:image/png;base64,{{ $order->payment_details['qr_code_base64'] }}" alt="QR Code Pix">
                            </div>
                            <p class="text-text-muted text-sm">Ou use o código Copia e Cola:</p>
                            <div class="flex">
                                <input type="text" readonly value="{{ $order->payment_details['qr_code'] }}" id="pix-code" class="form-input text-xs !rounded-r-none">
                                <button onclick="copyToClipboard()" class="bg-primary-light text-white px-4 rounded-r-lg hover:bg-primary-dark transition-colors">Copiar</button>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-900/50 border border-yellow-700 text-yellow-300 p-4 rounded-lg text-center animate-pulse">
                            <p class="font-bold">Processando Pagamento</p>
                            <p class="text-sm">Aguardando a geração dos dados de pagamento...</p>
                        </div>
                    @endif
                </div>
            @elseif ($order->status === 'paid')
                <div class="bg-green-900/50 border border-green-700 text-green-300 p-4 rounded-lg text-center">
                    <p class="font-bold">🎉 Pagamento Confirmado! 🎉</p>
                </div>
            @else
                 <div class="bg-red-900/50 border border-red-700 text-red-300 p-4 rounded-lg text-center">
                    <p class="font-bold">Pedido Expirado ou Cancelado</p>
                </div>
            @endif
        </div>

        <div class="bg-bg-secondary rounded-2xl border border-border p-6">
            <div class="flex items-center gap-4 mb-4">
                <img src="{{ $order->raffle->getFirstMediaUrl('raffles', 'thumb') }}" class="w-20 h-20 rounded-lg object-cover">
                <div>
                    <h3 class="font-bold text-lg text-text-light">{{ $order->raffle->title }}</h3>
                </div>
            </div>
            <hr class="border-border my-4">
            <h4 class="font-semibold text-text-light mb-2">Resumo da sua Compra</h4>
            <div class="flex justify-between items-center text-text-muted">
                <p>{{ $order->ticket_quantity }} Cota(s) selecionada(s)</p>
                <p class="font-semibold text-text-light">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
            </div>
            <div class="mt-4">
                <p class="font-semibold text-text-light mb-2">Seus Números:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($order->tickets as $ticket)
                        <span class="bg-primary-dark text-white font-mono text-sm font-bold py-1 px-3 rounded-full">{{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function timer(expiry) { return { expiry: expiry, time_left: '', init() { this.set_time(); setInterval(() => { this.set_time(); }, 1000); }, set_time() { const diff = new Date(this.expiry) - new Date(); if (diff < 0) { return this.time_left = 'Expirado!'; } const minutes = Math.floor(diff / 60000); const seconds = Math.floor((diff % 60000) / 1000); this.time_left = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`; } } }
            function copyToClipboard() { const input = document.getElementById('pix-code'); input.select(); document.execCommand('copy'); alert('Código Pix copiado!'); }
        </script>
    @endpush
</div>
