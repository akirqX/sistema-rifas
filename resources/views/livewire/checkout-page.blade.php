<div>
    <div class="container mx-auto px-4 py-16" @if(!$hasError && $order) wire:poll.5s="checkPaymentStatus" @endif>
        @if ($hasError || !$item)
            <div class="max-w-md mx-auto text-center">
                <h1 class="text-3xl font-bold text-red-400 mb-2">Ops! Algo deu errado.</h1>
                <p class="text-text-muted mb-6">{{ $errorMessage ?: 'Sua sessão de compra expirou ou já foi processada.' }}</p>
                <a href="{{ route('home') }}" class="cta-primary">Voltar para o Início</a>
            </div>
        @else
            <h1 class="text-3xl font-bold text-center text-white mb-2">Finalize o Pagamento</h1>
            <p class="text-center text-text-muted mb-8">Pague com PIX para garantir sua compra. O QR Code expira em breve.</p>
            <div class="max-w-4xl mx-auto bg-bg-secondary border border-border rounded-2xl p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                    <div class="text-center flex flex-col items-center border-b md:border-b-0 md:border-r border-border pb-8 md:pb-0 md:pr-8">
                        <h2 class="text-xl font-semibold text-text-light mb-4">Pague com PIX</h2>
                        <div class="bg-white p-4 rounded-lg">
                            @if($qrCodeBase64)
                                <img src="data:image/png;base64, {{ $qrCodeBase64 }}" alt="PIX QR Code" class="w-48 h-48 mx-auto">
                            @else
                                <div class="w-48 h-48 mx-auto flex items-center justify-center bg-gray-200 rounded-lg">
                                    <div wire:loading class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                                </div>
                            @endif
                        </div>
                        @if($expiresAt)
                        <div x-data="timer(new Date('{{ $expiresAt->toIso8601String() }}'))" x-init="init()" class="mt-4 font-mono">
                            <template x-if="!isExpired()"><p class="text-text-muted">Expira em: <span class="text-2xl text-primary-light font-bold" x-text="timeString"></span></p></template>
                            <template x-if="isExpired()"><p class="text-2xl text-red-500 font-bold">QR Code Expirado!</p></template>
                        </div>
                        @endif
                        @if($qrCodeCopyPaste)
                        <div x-data="{ text: 'Copiar Código PIX' }" class="w-full max-w-xs mt-4">
                            <button @click="navigator.clipboard.writeText('{{ $qrCodeCopyPaste }}'); text = 'Copiado!'; setTimeout(() => { text = 'Copiar Código PIX' }, 2000)" class="w-full font-semibold py-2 px-4 rounded-lg transition-colors duration-200 bg-primary-dark hover:bg-primary-light text-white"><span x-text="text"></span></button>
                        </div>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-xl font-semibold text-text-light mb-4">Resumo da Compra</h2>
                        <div class="space-y-4 flex-grow">
                            <div class="flex justify-between items-center"><span class="text-text-muted">Status:</span><x-order-status-badge :status="$paymentStatus" /></div>
                            <div class="flex justify-between items-center border-t border-border pt-4"><span class="font-semibold text-white">Item:</span><span class="text-white text-right">{{ $item->title ?? $item->name }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-text-muted">Quantidade:</span><span class="text-white">{{ $quantity }} @if($type === 'raffle') cota(s) @endif</span></div>
                            <div class="flex justify-between items-center text-xl font-bold border-t border-border pt-4 mt-4"><span class="text-white">Total:</span><span class="text-primary-light">R$ {{ number_format($totalAmount, 2, ',', '.') }}</span></div>
                        </div>
                        @if($type === 'raffle' && !empty($details['tickets']))
                            <div class="pt-6 mt-6 border-t border-border"><h3 class="font-semibold text-text-light mb-3">Seus números:</h3><div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">@foreach($details['tickets'] as $ticket)<span class="bg-bg-tertiary text-white font-mono text-sm py-1 px-3 rounded">{{ $ticket }}</span>@endforeach</div></div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timer', (expiryDate) => ({
            expiry: expiryDate, timeString: '--:--', interval: null,
            init() { this.updateTimer(); this.interval = setInterval(() => { this.updateTimer(); }, 1000); },
            updateTimer() {
                const totalSeconds = Math.max(0, Math.floor((this.expiry.getTime() - new Date().getTime()) / 1000));
                if (totalSeconds <= 0) { this.timeString = 'Expirado'; clearInterval(this.interval); return; }
                const minutes = Math.floor(totalSeconds / 60); const seconds = totalSeconds % 60;
                this.timeString = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            },
            isExpired() { return this.timeString === 'Expirado'; }
        }));
    });
</script>
@endpush
