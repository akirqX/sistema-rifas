<div>
    <div class="container mx-auto px-4 py-8">
        @if (session()->has('success')) <div class="p-4 mb-4 text-sm text-green-300 border border-green-500/30 rounded-lg bg-green-500/20">{{ session('success') }}</div> @endif
        @if (session()->has('error')) <div class="p-4 mb-4 text-sm text-red-300 border border-red-500/30 rounded-lg bg-red-500/20">{{ session('error') }}</div> @endif

        <div class="mb-8">
            <a href="{{ url()->previous() }}" class="text-sm text-text-muted hover:text-white transition-colors"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
            <h1 class="text-3xl font-bold text-white mt-2">Gerenciador de Cotas</h1>
            <p class="text-primary-light">Rifa: {{ $raffle->title }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border"><h4 class="text-sm font-medium text-text-muted">Pagas</h4><p class="text-3xl font-bold text-green-400 mt-2">{{ $paidCount }}</p></div>
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border"><h4 class="text-sm font-medium text-text-muted">Reservadas</h4><p class="text-3xl font-bold text-yellow-400 mt-2">{{ $reservedCount }}</p></div>
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border"><h4 class="text-sm font-medium text-text-muted">Expiradas</h4><p class="text-3xl font-bold text-blue-400 mt-2">{{ $expiredCount }}</p></div>
            <div class="bg-bg-secondary p-6 rounded-2xl border border-border"><h4 class="text-sm font-medium text-text-muted">Disponíveis</h4><p class="text-3xl font-bold text-text-muted mt-2">{{ $availableCount }}</p></div>
        </div>

        <div class="bg-bg-secondary p-4 sm:p-6 rounded-2xl border border-border">
            <h3 class="font-bold text-lg mb-4 text-white">Todas as Cotas</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-8 xl:grid-cols-10 gap-3">
                @php $padding = strlen((string)$totalTickets); @endphp
                @for ($i = 1; $i <= $totalTickets; $i++)
                    @php
                        $number = str_pad($i, $padding, '0', STR_PAD_LEFT);
                        $ticket = $ticketMap[$i] ?? null;
                    @endphp
                    <button @if($ticket) wire:click="openTicketModal({{ $ticket->id }})" @endif class="relative group rounded-lg text-center py-2 px-1 border-2
                        @if(!$ticket) border-gray-700/50 bg-gray-800/20
                        @elseif($ticket->status === 'paid') border-green-500/80 bg-green-500/20
                        @elseif($ticket->status === 'reserved') border-yellow-500/80 bg-yellow-500/10
                        @elseif($ticket->status === 'expired') border-blue-500/80 bg-blue-500/20
                        @endif {{ $ticket ? 'cursor-pointer hover:bg-opacity-40' : 'cursor-default' }} transition-colors">
                        <span class="font-bold text-lg text-white block">#{{ $number }}</span>
                        @php
                            $buyerName = optional($ticket->user)->name ?? optional($ticket->order)->guest_name;
                            $statusText = $buyerName ? \Illuminate\Support\Str::limit($buyerName, 15) : ucfirst($ticket->status ?? 'Disponível');
                        @endphp
                        <span class="text-xs text-text-muted block truncate" title="{!! strip_tags($statusText) !!}">{!! $statusText !!}</span>
                    </button>
                @endfor
            </div>
        </div>
    </div>

    @if($showTicketModal && $selectedTicket)
        <div x-data="{ show: @entangle('showTicketModal') }" x-show="show" x-on:keydown.escape.window="show = false" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4" style="display: none;">
            <div x-show="show" x-transition @click.away="show = false" class="bg-bg-secondary rounded-2xl border border-border shadow-lg w-full max-w-lg p-8">
                <div class="flex justify-between items-start">
                    <h2 class="text-2xl font-bold text-white mb-2">Detalhes da Cota <span class="text-primary-light">#{{ $selectedTicket->number }}</span></h2>
                    <x-order-status-badge :status="$selectedTicket->status" />
                </div>
                <hr class="border-border my-4">
                {{-- CORREÇÃO: Adicionado um @if para verificar se o pedido existe --}}
                @if($selectedTicket->order)
                    <div class="space-y-3 text-text-light">
                        <div class="flex"><p class="w-1/3 text-text-muted">Comprador:</p><p class="font-semibold">{{ $selectedTicket->order->getBuyerName() ?? 'N/A' }}</p></div>
                        <div class="flex"><p class="w-1/3 text-text-muted">E-mail:</p><p class="font-semibold">{{ $selectedTicket->order->getBuyerEmail() ?? 'N/A' }}</p></div>
                        <div class="flex"><p class="w-1/3 text-text-muted">Pedido ID:</p><p class="font-semibold">{{ $selectedTicket->order_id ?? 'N/A' }}</p></div>
                        <div class="flex"><p class="w-1/3 text-text-muted">Data da Reserva:</p><p class="font-semibold">{{ $selectedTicket->order->created_at->format('d/m/Y H:i') ?? 'N/A' }}</p></div>
                    </div>
                    <div class="mt-8 flex flex-col md:flex-row-reverse gap-4">
                        @if($selectedTicket->status !== 'paid')
                        <button wire:click="approveTicket" wire:confirm="Tem certeza que deseja APROVAR este pagamento manualmente? Esta ação é permanente." class="cta-primary flex-1">Aprovar Manualmente</button>
                        <button wire:click="forceCancelTicket" wire:confirm="Tem certeza que deseja LIBERAR esta cota? O comprador perderá a reserva." class="cta-danger flex-1">Liberar Cota (Forçado)</button>
                        @else
                        <p class="text-center text-green-400 w-full">Este pedido já foi pago e finalizado.</p>
                        @endif
                    </div>
                @else
                    <p class="text-center text-text-muted">Esta cota não está associada a nenhum pedido.</p>
                @endif
            </div>
        </div>
    @endif
</div>
