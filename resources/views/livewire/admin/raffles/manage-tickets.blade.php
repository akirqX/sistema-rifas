<div>
    {{-- Bloco para exibir mensagens --}}
    @if (session()->has('success')) <div class="p-4 mb-4 text-sm text-green-300 border border-green-500/30 rounded-lg bg-green-500/20" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">{{ session('success') }}</div> @endif
    @if (session()->has('error')) <div class="p-4 mb-4 text-sm text-red-300 border border-red-500/30 rounded-lg bg-red-500/20" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">{{ session('error') }}</div> @endif

    <div class="mb-8">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-text-muted hover:text-white transition-colors"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <h1 class="text-3xl font-bold text-white mt-2">Gerenciador de Cotas</h1>
        <p class="text-primary-light">Rifa: {{ $raffle->title }}</p>
    </div>

    {{-- CARDS DE ESTATÍSTICAS CORRIGIDOS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle"><h4 class="text-sm font-medium text-text-muted">Pagas</h4><p class="text-3xl font-bold text-green-400 mt-2">{{ $paidCount }}</p></div>
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle"><h4 class="text-sm font-medium text-text-muted">Pendentes</h4><p class="text-3xl font-bold text-yellow-400 mt-2">{{ $pendingCount }}</p></div>
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle"><h4 class="text-sm font-medium text-text-muted">Expiradas</h4><p class="text-3xl font-bold text-blue-400 mt-2">{{ $expiredCount }}</p></div>
        <div class="bg-panel-dark p-6 rounded-2xl border border-red-500/30"><h4 class="text-sm font-medium text-red-400">Órfãs (ERRO)</h4><p class="text-3xl font-bold text-red-400 mt-2">{{ $orphanCount }}</p></div>
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle"><h4 class="text-sm font-medium text-text-muted">Disponíveis</h4><p class="text-3xl font-bold text-text-muted mt-2">{{ $totalTickets - ($paidCount + $pendingCount + $expiredCount + $orphanCount) }}</p></div>
    </div>

    <div class="bg-panel-dark p-4 rounded-2xl border border-border-subtle">
        <h3 class="font-bold text-lg mb-4 text-white">Todas as Cotas</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-8 xl:grid-cols-10 gap-3">
            @for ($number = 1; $number <= $totalTickets; $number++)
                @php
                    $ticket = $ticketMap[$number] ?? null;
                    $statusClass = 'border-gray-700/50 bg-gray-800/20'; // Disponível (Padrão)
                    $statusText = 'Disponível';
                    $isCancellable = false;

                    if ($ticket) {
                        if ($ticket->status === 'paid' && $ticket->user_id) {
                            $statusClass = 'border-green-500/80 bg-green-500/20';
                            $statusText = optional($ticket->user)->name ?? 'Pago';
                        } elseif ($ticket->status === 'paid' && !$ticket->user_id) {
                            $statusClass = 'border-red-500/80 bg-red-500/20';
                            $statusText = '<span class="text-red-400">ÓRFÃ</span>';
                            $isCancellable = true;
                        } elseif ($ticket->status === 'pending' || $ticket->status === 'reserved') {
                            $statusClass = 'border-yellow-500/80 bg-yellow-500/10';
                            $statusText = optional($ticket->user)->name ?? 'Pendente';
                            $isCancellable = true;
                        } elseif ($ticket->status === 'expired') {
                            $statusClass = 'border-blue-500/80 bg-blue-500/20';
                            $statusText = 'Expirada';
                            $isCancellable = true;
                        }
                    }
                @endphp

                <div class="relative group rounded-lg text-center py-2 px-1 border-2 {{ $statusClass }}">
                    <span class="font-bold text-lg text-white block">#{{ str_pad($number, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-xs text-text-muted block truncate" title="{!! strip_tags($statusText) !!}">{!! $statusText !!}</span>

                    @if ($isCancellable)
                        <div class="absolute inset-0 flex items-center justify-center bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity rounded-md">
                            <button wire:click="forceCancelTicket({{ $ticket->id }})"
                                    wire:confirm="Tem certeza que deseja LIBERAR a cota #{{ $number }}? Esta ação não pode ser desfeita."
                                    class="text-red-400 hover:text-red-300 font-bold text-sm"
                                    title="Liberar Cota (Forçado)">
                                Liberar
                            </button>
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>
</div>
