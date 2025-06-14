<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- 1. O BOTÃO PARA ADICIONAR NOVA RIFA -->
            <div class="flex justify-end mb-4">
                <button wire:click="openCreateModal"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    + Nova Rifa
                </button>
            </div>

            <!-- 2. MENSAGENS DE SUCESSO/ERRO -->
            @if (session()->has('success'))
                <div class="bg-green-200 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="bg-red-200 text-red-800 p-4 rounded mb-4">{{ session('error') }}</div>
            @endif

            <!-- 3. O CARD BRANCO COM A TABELA QUE VOCÊ JÁ TINHA -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">ID</th>
                                    <th class="py-2 px-4 border-b">Título</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Cotas Vendidas</th>
                                    <th class="py-2 px-4 border-b">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($raffles as $raffle)
                                    <tr class="text-center">
                                        <td class="py-2 px-4 border-b">{{ $raffle->id }}</td>
                                        <td class="py-2 px-4 border-b">{{ $raffle->title }}</td>
                                        <td class="py-2 px-4 border-b">{{ $raffle->status }}</td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $raffle->tickets()->where('status', 'paid')->count() }} /
                                            {{ $raffle->total_tickets }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
    @if ($raffle->status === 'pending')
        <button wire:click="activateRaffle({{ $raffle->id }})" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1 px-2 rounded">
            Ativar
        </button>
    @else
        <button class="text-blue-500 hover:underline">Editar</button>
    @endif
</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center">Nenhuma rifa encontrada.</td>
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

    <!-- 4. A JANELA (MODAL) PARA CRIAR A RIFA - SÓ APARECE QUANDO O BOTÃO É CLICADO -->
    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
                <h2 class="text-2xl font-bold mb-4">Criar Nova Rifa</h2>
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Título -->
                        <div class="col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" wire:model.defer="title" id="title"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- Descrição -->
                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea wire:model.defer="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- Preço da Cota -->
                        <div>
                            <label for="ticket_price" class="block text-sm font-medium text-gray-700">Preço por Cota
                                (R$)</label>
                            <input type="number" step="0.01" wire:model.defer="ticket_price" id="ticket_price"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('ticket_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- Quantidade de Cotas -->
                        <div>
                            <label for="total_tickets" class="block text-sm font-medium text-gray-700">Quantidade de
                                Cotas</label>
                            <input type="number" wire:model.defer="total_tickets" id="total_tickets"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('total_tickets') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <!-- Botões da Modal -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" wire:click="closeCreateModal"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Salvar Rifa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
