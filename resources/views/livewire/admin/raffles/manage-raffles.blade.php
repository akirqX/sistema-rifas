<div class="space-y-8">
    {{-- Bloco para exibir mensagens de sucesso ou erro --}}
    @if (session()->has('success'))
        <div class="p-4 mb-2 text-sm text-green-300 border border-green-500/30 rounded-lg bg-green-500/20" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
         <div class="p-4 mb-2 text-sm text-red-300 border border-red-500/30 rounded-lg bg-red-500/20" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">{{ session('error') }}</div>
    @endif

    {{-- GERENCIAMENTO DE RIFAS --}}
    <div class="bg-panel-dark border border-border-subtle rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                <h3 class="text-2xl font-bold text-white mb-3 sm:mb-0">Gerenciamento de Rifas</h3>
                <button wire:click="openRaffleModal" class="btn-prodgio btn-primary w-full sm:w-auto justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Nova Rifa</span>
                </button>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchRaffles" placeholder="Buscar rifas pelo título..." class="form-input w-full mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-bg-tertiary">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-semibold text-text-muted uppercase tracking-wider">Rifa</th>
                            <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Progresso</th>
                            <th class="py-3 px-6 text-left text-xs font-semibold text-text-muted uppercase tracking-wider">Vencedor(a)</th>
                            <th class="py-3 px-6 text-right text-xs font-semibold text-text-muted uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse ($raffles as $raffle)
                            <tr wire:key="raffle-{{ $raffle->id }}" class="hover:bg-gray-800/50">
                                <td class="py-4 px-6 whitespace-nowrap"><div class="flex items-center"><div class="flex-shrink-0 h-12 w-16"><img class="h-12 w-16 object-cover rounded-md" src="{{ $raffle->getFirstMediaUrl('raffles', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Rifa' }}" alt=""></div><div class="ml-4"><div class="text-sm font-medium text-white">{{ $raffle->title }}</div><div class="text-xs text-text-subtle">ID: {{ $raffle->id }} | R$ {{ number_format($raffle->price, 2, ',', '.') }}</div></div></div></td>
                                <td class="py-4 px-6 whitespace-nowrap text-center"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $raffle->status === 'active' ? 'bg-green-500/20 text-green-400' : ($raffle->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : ($raffle->status === 'finished' ? 'bg-blue-500/20 text-blue-400' : 'bg-red-500/20 text-red-400')) }}">{{ ucfirst($raffle->status) }}</span></td>
                                <td class="py-4 px-6 whitespace-nowrap text-center text-sm text-text-muted">{{ $raffle->tickets()->where('status', 'paid')->count() }} / {{ $raffle->total_numbers }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-text-muted">@if($raffle->winnerTicket) Cota: <strong>{{ str_pad($raffle->winnerTicket->number, 4, '0', STR_PAD_LEFT) }}</strong><div class="text-xs">{{ $raffle->winnerTicket->user->name ?? 'Usuário' }}</div>@else - @endif</td>
                                <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('admin.raffles.tickets', $raffle) }}" class="text-gray-400 hover:text-white" title="Gerenciar Cotas">Cotas</a>
                                        <button wire:click="editRaffle({{ $raffle->id }})" class="text-indigo-400 hover:text-indigo-300" title="Editar">Editar</button>
                                        @if ($raffle->status === 'pending')<button wire:click="activateRaffle({{ $raffle->id }})" class="text-green-400 hover:text-green-300" title="Ativar">Ativar</button>@endif
                                        @if ($raffle->status === 'active')
                                            <button wire:click="openDrawModal({{ $raffle->id }})" class="text-blue-400 hover:text-blue-300" title="Sorteio Manual">Sortear</button>
                                            <button wire:click="performRandomDraw({{ $raffle->id }})" wire:confirm="Tem certeza que deseja realizar o sorteio aleatório AGORA? Esta ação é irreversível." class="text-purple-400 hover:text-purple-300" title="Sorteio Aleatório">Aleatório</button>
                                        @endif
                                        @if ($raffle->status !== 'finished' && $raffle->status !== 'cancelled')<button wire:click="cancelRaffle({{ $raffle->id }})" wire:confirm="Tem certeza que deseja cancelar esta rifa?" class="text-red-400 hover:text-red-300" title="Cancelar">Cancelar</button>@endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-12 text-center text-text-muted">Nenhuma rifa encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $raffles->links(data: ['scrollTo' => false]) }}</div>
        </div>
    </div>

    {{-- MODAL PARA CRIAR/EDITAR RIFA --}}
    @if ($showRaffleModal)
        <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4" x-data x-trap.noscroll.inert="showRaffleModal">
            <div class="bg-panel-dark p-8 rounded-2xl w-full max-w-2xl border border-border-subtle max-h-screen overflow-y-auto">
                <h2 class="text-2xl font-bold text-white mb-6">{{ $editingRaffle ? 'Editar Rifa' : 'Criar Nova Rifa' }}</h2>
                <form wire:submit.prevent="saveRaffle" class="space-y-4">
                    <div><label for="title" class="form-label">Título</label><input type="text" id="title" wire:model.defer="title" class="form-input">@error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    <div><label for="description" class="form-label">Descrição</label><textarea id="description" wire:model.defer="description" rows="3" class="form-textarea"></textarea>@error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label for="raffle_price" class="form-label">Preço por Cota (R$)</label><input type="number" step="0.01" id="raffle_price" wire:model.defer="raffle_price" class="form-input">@error('raffle_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                        <div><label for="total_numbers" class="form-label">Quantidade de Cotas</label><input type="number" id="total_numbers" wire:model.defer="total_numbers" class="form-input" @if($editingRaffle) disabled @endif>@if($editingRaffle) <span class="text-xs text-text-muted">A quantidade não pode ser alterada.</span> @endif @error('total_numbers') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    </div>
                    <div><label for="raffle_photo" class="form-label">Imagem da Rifa</label><input type="file" id="raffle_photo" wire:model="raffle_photo" class="form-input-file">@if ($raffle_photo) <img src="{{ $raffle_photo->temporaryUrl() }}" class="mt-4 h-32 w-auto rounded">@elseif($editingRaffle && $editingRaffle->getFirstMedia('raffles')) <img src="{{ $editingRaffle->getFirstMediaUrl('raffles') }}" class="mt-4 h-32 w-auto rounded" alt="Imagem atual">@endif @error('raffle_photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    <div class="flex justify-end gap-4 pt-4"><button type="button" @click="$wire.set('showRaffleModal', false)" class="btn-prodgio btn-secondary">Cancelar</button><button type="submit" class="btn-prodgio btn-primary" wire:loading.attr="disabled"><span wire:loading.remove>Salvar Rifa</span><span wire:loading>Salvando...</span></button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL DE SORTEIO MANUAL --}}
    @if ($showDrawModal)
        <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4" x-data x-trap.noscroll.inert="showDrawModal">
            <div class="bg-panel-dark p-8 rounded-2xl w-full max-w-md border border-border-subtle">
                <h2 class="text-2xl font-bold text-white mb-6 text-center">Definir Ganhador Manualmente</h2>
                <form wire:submit.prevent="setWinner" class="space-y-4">
                    <div>
                        <label for="winner_ticket_number" class="form-label">Número da Cota Vencedora</label>
                        <input type="number" wire:model.defer="winner_ticket_number" id="winner_ticket_number" class="form-input text-center text-lg" placeholder="Ex: 0042">
                        @error('winner_ticket_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" @click="$wire.set('showDrawModal', false)" class="btn-prodgio btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-prodgio btn-primary" wire:loading.attr="disabled">Confirmar Ganhador</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL PARA VISUALIZAR COTAS --}}
    @if ($showTicketsModal)
        <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4" x-data x-trap.noscroll.inert="showTicketsModal">
            <div class="bg-panel-dark p-8 rounded-2xl w-full max-w-4xl border border-border-subtle max-h-[90vh] flex flex-col">
                <h2 class="text-2xl font-bold text-white mb-6">Cotas da Rifa: <span class="text-primary-light">{{ $raffleForTickets?->title }}</span></h2>
                <div class="overflow-y-auto flex-grow">
                    <table class="min-w-full">
                         <thead class="bg-bg-tertiary sticky top-0">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-text-muted uppercase">Cota</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-text-muted uppercase">Comprador</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-text-muted uppercase">Status</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-text-muted uppercase">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse($ticketsForModal as $ticket)
                                <tr>
                                    <td class="p-4 whitespace-nowrap text-sm font-bold text-white">#{{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="p-4 whitespace-nowrap text-sm text-text-light">{{ $ticket->user?->name ?? 'N/A' }} <br> <span class="text-xs text-text-muted">{{ $ticket->user?->email }}</span></td>
                                    <td class="p-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ticket->status === 'paid' ? 'bg-green-500/20 text-green-400' : ($ticket->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-gray-500/20 text-gray-400') }}">{{ ucfirst($ticket->status) }}</span></td>
                                    <td class="p-4 whitespace-nowrap text-sm text-text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-12 text-center text-text-muted">Nenhuma cota encontrada para esta rifa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <div class="pt-6 text-right">
                    <button type="button" @click="$wire.set('showTicketsModal', false)" class="btn-prodgio btn-secondary">Fechar</button>
                </div>
            </div>
        </div>
    @endif
</div>
