<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Botão e Mensagens de Feedback -->
            <div class="flex justify-end mb-4">
                <button wire:click="create" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                    + Nova Rifa
                </button>
            </div>

            @if (session()->has('success'))
                <div class="bg-green-200 text-green-800 p-4 rounded-lg mb-4 shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="bg-red-200 text-red-800 p-4 rounded-lg mb-4 shadow-sm">{{ session('error') }}</div>
            @endif

            <!-- Card Branco com a Nova Tabela -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rifa</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Progresso</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencedor(a)</th>
                                    <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($raffles as $raffle)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-16">
                                                    <img class="h-12 w-16 object-cover rounded-md" src="{{ $raffle->getFirstMediaUrl('raffles', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Rifa' }}" alt="Imagem da Rifa">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $raffle->title }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $raffle->id }} | Preço: R$ {{ number_format($raffle->ticket_price, 2, ',', '.') }}</div>
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
                                        <td class="py-4 px-6 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $raffle->tickets()->where('status', 'paid')->count() }} / {{ $raffle->total_tickets }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-500">
                                            @if($raffle->winner)
                                                Cota: <strong>{{ str_pad($raffle->winner->number, 4, '0', STR_PAD_LEFT) }}</strong>
                                                <div class="text-xs">{{ $raffle->winner->user->name ?? 'Usuário' }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                            {{-- 👇👇👇 NOVO LINK ADICIONADO AQUI 👇👇👇 --}}
                                            <a href="{{ route('admin.raffles.tickets', $raffle) }}" class="text-gray-600 hover:text-gray-900">Cotas</a>

                                            <button wire:click="edit({{ $raffle->id }})" class="ml-4 text-indigo-600 hover:text-indigo-900">Editar</button>

                                            @if ($raffle->status === 'pending')
                                                <button wire:click="activateRaffle({{ $raffle->id }})" class="ml-4 text-green-600 hover:text-green-900">Ativar</button>
                                            @endif

                                            @if ($raffle->status === 'active')
                                                <button wire:click="performDraw({{ $raffle->id }})" wire:confirm="Tem certeza que deseja realizar o sorteio AGORA? Esta ação é irreversível." class="ml-4 text-purple-600 hover:text-purple-900">Sortear!</button>
                                            @endif

                                            @if ($raffle->status !== 'finished' && $raffle->status !== 'cancelled')
                                                <button wire:click="cancelRaffle({{ $raffle->id }})" wire:confirm="Tem certeza que deseja cancelar esta rifa?" class="ml-4 text-red-600 hover:text-red-900">Cancelar</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-gray-500">Nenhuma rifa encontrada. Clique em "+ Nova Rifa" para começar.</td>
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
    </div>

    <!-- Modal para Criar/Editar Rifa -->
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
                <h2 class="text-2xl font-bold mb-4">{{ $editingRaffle ? 'Editar Rifa' : 'Criar Nova Rifa' }}</h2>

                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" wire:model.defer="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea wire:model.defer="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label for="photo" class="block text-sm font-medium text-gray-700">Imagem da Rifa</label>
                            <input type="file" wire:model="photo" id="photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                            <div wire:loading wire:target="photo" class="mt-2 text-sm text-gray-500">Carregando preview...</div>
                            @if ($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="mt-4 h-32 w-auto rounded">
                            @elseif($editingRaffle && $editingRaffle->getFirstMedia('raffles'))
                                <img src="{{ $editingRaffle->getFirstMediaUrl('raffles') }}" class="mt-4 h-32 w-auto rounded" alt="Imagem atual">
                            @endif
                            @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="ticket_price" class="block text-sm font-medium text-gray-700">Preço por Cota (R$)</label>
                            <input type="number" step="0.01" wire:model.defer="ticket_price" id="ticket_price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('ticket_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="total_tickets" class="block text-sm font-medium text-gray-700">Quantidade de Cotas</label>
                            <input type="number" wire:model.defer="total_tickets" id="total_tickets" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if($editingRaffle) disabled @endif>
                            @if($editingRaffle)
                                <span class="text-xs text-gray-500">A quantidade de cotas não pode ser alterada.</span>
                            @endif
                            @error('total_tickets') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
