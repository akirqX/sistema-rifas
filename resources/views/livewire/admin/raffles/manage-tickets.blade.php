<div>
    <div class="container mx-auto px-4 py-8">
        @if (session()->has('success')) <div class="p-4 mb-4 text-sm text-green-300 border border-green-500/30 rounded-lg bg-green-500/20">{{ session('success') }}</div> @endif
        @if (session()->has('error')) <div class="p-4 mb-4 text-sm text-red-300 border border-red-500/30 rounded-lg bg-red-500/20">{{ session('error') }}</div> @endif

        <div class="mb-8">
            <a href="{{ url()->previous() }}" class="text-sm text-text-muted hover:text-white transition-colors"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
            <h1 class="text-3xl font-bold text-white mt-2">Gerenciador de Cotas</h1>
            <p class="text-primary-light">Rifa: {{ $raffle->title }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <button wire:click="setStatusFilter('paid')" class="text-left bg-bg-secondary p-6 rounded-2xl border-2 transition-all {{ $filterStatus === 'paid' ? 'border-green-400 scale-105' : 'border-border' }}"><h4 class="text-sm font-medium text-text-muted">Pagas</h4><p class="text-3xl font-bold text-green-400 mt-2">{{ $paidCount }}</p></button>
            <button wire:click="setStatusFilter('reserved')" class="text-left bg-bg-secondary p-6 rounded-2xl border-2 transition-all {{ $filterStatus === 'reserved' ? 'border-yellow-400 scale-105' : 'border-border' }}"><h4 class="text-sm font-medium text-text-muted">Reservadas</h4><p class="text-3xl font-bold text-yellow-400 mt-2">{{ $reservedCount }}</p></button>
            <button wire:click="setStatusFilter('expired')" class="text-left bg-bg-secondary p-6 rounded-2xl border-2 transition-all {{ $filterStatus === 'expired' ? 'border-blue-400 scale-105' : 'border-border' }}"><h4 class="text-sm font-medium text-text-muted">Expiradas</h4><p class="text-3xl font-bold text-blue-400 mt-2">{{ $expiredCount }}</p></button>
            <div class="bg-bg-secondary p-6 rounded-2xl border-2 {{ $filterStatus === 'available' ? 'border-gray-500 scale-105' : 'border-border' }}"><h4 class="text-sm font-medium text-text-muted">Disponíveis</h4><p class="text-3xl font-bold text-text-muted mt-2">{{ $availableCount }}</p></div>
        </div>

        <div class="bg-bg-secondary p-4 sm:p-6 rounded-2xl border border-border">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                <div class="w-full sm:w-auto">
                    <button wire:click="openCreateModal" class="btn-prodgio btn-secondary text-sm w-full"><i class="fas fa-plus mr-2"></i>Criar Cota Manual</button>
                </div>
                <div class="relative w-full sm:w-1/3">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar cota ou comprador..." class="bg-panel-dark border border-border-subtle rounded-lg py-2 pl-10 pr-4 w-full focus:ring-primary-purple focus:border-primary-purple">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-search text-text-muted"></i></div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-400">
                    <thead class="text-xs text-gray-400 uppercase bg-panel-dark">
                        <tr>
                            <th scope="col" class="px-6 py-3">Cota</th>
                            <th scope="col" class="px-6 py-3">Comprador</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Data</th>
                            <th scope="col" class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            <tr class="border-b bg-bg-secondary border-border hover:bg-panel-dark">
                                <td class="px-6 py-4 font-medium text-white">#{{ $ticket->number }}</td>
                                <td class="px-6 py-4">{{ $ticket->user->name ?? $ticket->order->getBuyerName() ?? 'N/A (Manual)' }}</td>
                                <td class="px-6 py-4"><x-order-status-badge :status="$ticket->status" /></td>
                                <td class="px-6 py-4">{{ $ticket->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="openTicketModal({{ $ticket->id }})" class="font-medium text-primary-purple hover:underline">Detalhes</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-text-muted">Nenhuma cota encontrada para os filtros aplicados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL DE DETALHES --}}
    @if($showTicketModal && $selectedTicket)
        <div
            x-data="{ show: @entangle('showTicketModal') }"
            x-show="show"
            x-on:keydown.escape.window="show = false"
            class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-[9999]"
            style="display: none;"
            wire:ignore.self
        >
            <div x-show="show" x-transition @click.away="show = false" class="bg-bg-secondary rounded-2xl border border-border shadow-lg w-full max-w-lg p-8">
                <div class="flex justify-between items-start">
                    <h2 class="text-2xl font-bold text-white mb-2">Detalhes da Cota <span class="text-primary-light">#{{ $selectedTicket->number }}</span></h2>
                    <x-order-status-badge :status="$selectedTicket->status" />
                </div>
                <hr class="border-border my-4">

                @if($selectedTicket->order)
                    <div class="space-y-3 text-text-light">
                        <div class="flex"><p class="w-1/3 text-text-muted">Comprador:</p><p class="font-semibold">{{ $selectedTicket->order->getBuyerName() ?? 'N/A' }}</p></div>
                        <div class="flex"><p class="w-1/3 text-text-muted">E-mail:</p><p class="font-semibold">{{ $selectedTicket->order->getBuyerEmail() ?? 'N/A' }}</p></div>
                        <div class="flex"><p class="w-1/3 text-text-muted">Pedido ID:</p><p class="font-semibold">{{ $selectedTicket->order_id ?? 'N/A' }}</p></div>
                        <div class="flex"><p class="w-1/3 text-text-muted">Data da Reserva:</p><p class="font-semibold">{{ $selectedTicket->order->created_at->format('d/m/Y H:i') ?? 'N/A' }}</p></div>
                    </div>
                    <div class="mt-8 flex flex-col md:flex-row-reverse gap-4">
                        @if($selectedTicket->status !== 'paid')
                        <button wire:click="approveTicket" wire:confirm="Tem certeza que deseja APROVAR este pagamento manualmente? Esta ação é permanente." class="btn-prodgio btn-primary flex-1">Aprovar Manualmente</button>
                        <button wire:click="forceCancelTicket" wire:confirm="Tem certeza que deseja LIBERAR esta cota? O comprador perderá a reserva." class="btn-prodgio btn-danger flex-1">Liberar Cota (Forçado)</button>
                        @else
                        <p class="text-center text-green-400 w-full">Este pedido já foi pago e finalizado.</p>
                        @endif
                    </div>
                @else
                    <div class="space-y-3 text-text-light">
                       <div class="flex"><p class="w-1/3 text-text-muted">Comprador:</p><p class="font-semibold">{{ $selectedTicket->user->name ?? 'Usuário não encontrado' }}</p></div>
                       <div class="flex"><p class="w-1/3 text-text-muted">E-mail:</p><p class="font-semibold">{{ $selectedTicket->user->email ?? 'N/A' }}</p></div>
                       <div class="flex"><p class="w-1/3 text-text-muted">Status:</p><p class="font-semibold">Criado Manualmente (Pago)</p></div>
                   </div>
                    <div class="mt-8 flex flex-col md:flex-row-reverse gap-4">
                        <button wire:click="forceCancelTicket" wire:confirm="Tem certeza que deseja DELETAR esta cota manual? Esta ação é permanente." class="btn-prodgio btn-danger flex-1">Deletar Cota Manual</button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- MODAL DE CRIAR COTA MANUAL --}}
    @if($showCreateModal)
        <div
            x-data="{ show: @entangle('showCreateModal') }"
            x-show="show"
            x-on:keydown.escape.window="show = false"
            class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-[9999]"
            style="display: none;"
        >
            <form wire:submit.prevent="createManualTicket" @click.away="show = false" class="bg-bg-secondary rounded-2xl border border-border shadow-lg w-full max-w-lg p-8">
                <h2 class="text-2xl font-bold text-white mb-4">Criar Cota Manualmente</h2>
                <div class="space-y-4">
                    <div>
                        <label for="newTicketNumber" class="block mb-2 text-sm font-medium text-text-light">Número da Cota</label>
                        <input type="number" id="newTicketNumber" wire:model="newTicketNumber" class="text-input" min="1" max="{{ $totalTickets }}">
                        @error('newTicketNumber') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="newTicketUserId" class="block mb-2 text-sm font-medium text-text-light">ID do Usuário</label>
                        <input type="number" id="newTicketUserId" wire:model="newTicketUserId" class="text-input">
                        @error('newTicketUserId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mt-8 flex gap-4">
                    <button type="button" @click="show = false" class="btn-prodgio btn-secondary flex-1">Cancelar</button>
                    <button type="submit" class="btn-prodgio btn-primary flex-1">Salvar Cota</button>
                </div>
            </form>
        </div>
    @endif
</div>
