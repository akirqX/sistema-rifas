<div>
    {{-- Bloco para exibir mensagens de sucesso ou erro com a nova estética --}}
    @if (session()->has('success'))
        <div class="container p-4 mx-auto">
            <div class="p-4 text-sm text-green-300 border border-green-500/30 rounded-md bg-green-500/20">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session()->has('error'))
         <div class="container p-4 mx-auto">
            <div class="p-4 text-sm text-red-300 border border-red-500/30 rounded-md bg-red-500/20">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="section">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h1 class="section-title">Painel do Administrador</h1>
            </div>

            <!-- WIDGETS DE ESTATÍSTICAS COM NOVA ESTÉTICA -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-bg-secondary p-6 rounded-2xl border border-border shadow-lg">
                    <h4 class="text-sm font-medium text-text-muted">Total Arrecadado</h4>
                    <p class="text-3xl font-bold text-primary-light mt-2">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                </div>
                <div class="bg-bg-secondary p-6 rounded-2xl border border-border shadow-lg">
                    <h4 class="text-sm font-medium text-text-muted">Pedidos Totais</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $totalOrders }}</p>
                </div>
                <div class="bg-bg-secondary p-6 rounded-2xl border border-border shadow-lg">
                    <h4 class="text-sm font-medium text-text-muted">Cotas Vendidas</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $totalTicketsSold }}</p>
                </div>
                <div class="bg-bg-secondary p-6 rounded-2xl border border-border shadow-lg">
                    <h4 class="text-sm font-medium text-text-muted">Rifas Ativas</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $activeRafflesCount }}</p>
                </div>
            </div>

            <!-- GRÁFICO E AÇÕES RÁPIDAS COM NOVA ESTÉTICA -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-2 bg-bg-secondary p-6 rounded-2xl border border-border shadow-lg" wire:ignore>
                    <h4 class="font-bold text-white mb-4">Vendas nos últimos 7 dias</h4>
                    <div class="h-64"><canvas id="salesChart"></canvas></div>
                </div>
                <div class="bg-bg-secondary p-6 rounded-2xl border border-border shadow-lg">
                     <h4 class="font-bold text-white mb-4">Ações Rápidas</h4>
                     <button wire:click="create" class="cta-primary w-full justify-center">
                        <i class="fas fa-plus mr-2"></i> Nova Rifa
                    </button>
                </div>
            </div>

            <!-- TABELA DE GERENCIAMENTO COM NOVA ESTÉTICA -->
            <div class="bg-bg-secondary border border-border rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-white mb-4">Gerenciamento de Rifas</h3>
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
                            <tbody class="divide-y divide-border">
                                @forelse ($raffles as $raffle)
                                    <tr class="hover:bg-bg-tertiary">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-16">
                                                    <img class="h-12 w-16 object-cover rounded-md" src="{{ $raffle->getFirstMediaUrl('raffles', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Rifa' }}" alt="Imagem da Rifa">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-white">{{ $raffle->title }}</div>
                                                    <div class="text-xs text-text-subtle">ID: {{ $raffle->id }} | Preço: R$ {{ number_format($raffle->price, 2, ',', '.') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $raffle->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $raffle->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $raffle->status === 'finished' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $raffle->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($raffle->status) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-center text-sm text-text-muted">
                                            {{ $raffle->tickets()->where('status', 'paid')->count() }} / {{ $raffle->total_numbers }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-sm text-text-muted">
                                            @if($raffle->winner)
                                                Cota: <strong>{{ str_pad($raffle->winner->number, 4, '0', STR_PAD_LEFT) }}</strong>
                                                <div class="text-xs">{{ $raffle->winner->user->name ?? 'Usuário' }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.raffles.tickets', $raffle) }}" class="text-gray-400 hover:text-white">Cotas</a>
                                            <button wire:click="edit({{ $raffle->id }})" class="ml-4 text-indigo-400 hover:text-indigo-300">Editar</button>
                                            @if ($raffle->status === 'pending')
                                                <button wire:click="activateRaffle({{ $raffle->id }})" class="ml-4 text-green-400 hover:text-green-300">Ativar</button>
                                            @endif
                                            {{-- NOVOS BOTÕES DE SORTEIO --}}
                                            @if ($raffle->status === 'active')
                                                <button wire:click="showDrawModal({{ $raffle->id }})" class="ml-4 text-primary-light hover:underline">Definir Ganhador</button>
                                                <button wire:click="performRandomDraw({{ $raffle->id }})" wire:confirm="Tem certeza que deseja realizar o sorteio aleatório AGORA? Esta ação é irreversível." class="ml-4 text-accent hover:underline">Sorteio Aleatório</button>
                                            @endif
                                            @if ($raffle->status !== 'finished' && $raffle->status !== 'cancelled')
                                                <button wire:click="cancelRaffle({{ $raffle->id }})" wire:confirm="Tem certeza que deseja cancelar esta rifa?" class="ml-4 text-red-400 hover:text-red-300">Cancelar</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-text-muted">Nenhuma rifa encontrada. Clique em "+ Nova Rifa" para começar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $raffles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Criar/Editar Rifa com Nova Estética -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-bg-secondary p-8 rounded-2xl shadow-2xl w-full max-w-2xl border border-border max-h-screen overflow-y-auto">
                <h2 class="font-heading text-2xl font-bold text-white mb-6">{{ $editingRaffle ? 'Editar Rifa' : 'Criar Nova Rifa' }}</h2>
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="title-modal" class="form-label">Título</label>
                            <input type="text" wire:model.defer="title" id="title-modal" class="form-input">
                            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label for="description-modal" class="form-label">Descrição</label>
                            <textarea wire:model.defer="description" id="description-modal" rows="3" class="form-textarea"></textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label for="photo-modal" class="form-label">Imagem da Rifa</label>
                            <input type="file" wire:model="photo" id="photo-modal" class="form-input">
                            @if ($photo) <img src="{{ $photo->temporaryUrl() }}" class="mt-4 h-32 w-auto rounded">
                            @elseif($editingRaffle && $editingRaffle->getFirstMedia('raffles')) <img src="{{ $editingRaffle->getFirstMediaUrl('raffles') }}" class="mt-4 h-32 w-auto rounded" alt="Imagem atual">@endif
                            @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="price-modal" class="form-label">Preço por Cota (R$)</label>
                            <input type="number" step="0.01" wire:model.defer="price" id="price-modal" class="form-input">
                            @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="total_numbers-modal" class="form-label">Quantidade de Cotas</label>
                            <input type="number" wire:model.defer="total_numbers" id="total_numbers-modal" class="form-input" @if($editingRaffle) disabled @endif>
                            @if($editingRaffle) <span class="text-xs text-text-muted">A quantidade não pode ser alterada.</span> @endif
                            @error('total_numbers') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" wire:click="closeModal" class="cta-secondary">Cancelar</button>
                        <button type="submit" class="cta-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- NOVO MODAL PARA SORTEIO MANUAL -->
    @if ($showDrawModal)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-bg-secondary p-8 rounded-2xl shadow-2xl w-full max-w-md border border-border">
                <h2 class="font-heading text-2xl font-bold text-white mb-6 text-center">Definir Ganhador Manualmente</h2>
                <form wire:submit.prevent="setWinner">
                    <div>
                        <label for="winner_ticket_number" class="form-label">Número da Cota Vencedora</label>
                        <input type="number" wire:model.defer="winner_ticket_number" id="winner_ticket_number" class="form-input text-center text-lg" placeholder="Ex: 0042">
                        @error('winner_ticket_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" wire:click="closeDrawModal" class="cta-secondary">Cancelar</button>
                        <button type="submit" class="cta-primary">Confirmar Ganhador</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
    {{-- Seu script de Chart.js --}}
    @endpush
</div>
