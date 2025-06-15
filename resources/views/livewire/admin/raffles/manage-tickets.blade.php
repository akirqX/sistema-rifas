<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gerenciando Cotas: <span class="text-blue-600">{{ $raffle->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- BARRA DE AÇÕES EM LOTE (SÓ APARECE QUANDO ALGO É SELECIONADO) -->
            @if ($selectedTickets)
            <div class="bg-indigo-600 p-4 rounded-lg shadow-lg mb-6 flex items-center justify-between">
                <span class="font-bold text-white">{{ count($selectedTickets) }} cotas selecionadas</span>
                <div>
                    <button wire:click="releaseSelected" wire:confirm="Tem certeza que deseja LIBERAR as cotas selecionadas? Elas voltarão a ficar disponíveis para compra." class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded-lg">
                        Liberar Selecionadas
                    </button>
                    {{-- Futuramente, outros botões de ação podem vir aqui --}}
                </div>
            </div>
            @endif

            <!-- PAINEL DE FILTROS -->
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Buscar por nº ou nome</label>
                        <input type="text" wire:model.live.debounce.500ms="search" id="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Digite para buscar...">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Filtrar por Status</label>
                        <select wire:model.live="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="all">Todos</option>
                            <option value="available">Disponíveis</option>
                            <option value="reserved">Reservadas</option>
                            <option value="paid">Pagas</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="selectAll" id="selectAll" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="selectAll" class="ml-2 block text-sm text-gray-900">Selecionar todos na página</label>
                    </div>
                </div>
            </div>

            <!-- MENSAGENS DE FEEDBACK -->
            @if (session()->has('success'))
                <div class="bg-green-200 text-green-800 p-4 rounded-lg mb-4 shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="bg-red-200 text-red-800 p-4 rounded-lg mb-4 shadow-sm">{{ session('error') }}</div>
            @endif

            <!-- GRADE DE COTAS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-5 sm:grid-cols-10 md:grid-cols-12 lg:grid-cols-15 gap-2">
                        @forelse ($tickets as $ticket)
                            <div class="relative rounded-md text-center font-mono font-semibold text-xs border
                                {{ in_array($ticket->id, $selectedTickets) ? 'ring-2 ring-indigo-500' : '' }}
                                {{ $ticket->status === 'paid' ? 'bg-red-500 text-white' : '' }}
                                {{ $ticket->status === 'reserved' ? 'bg-yellow-400 text-white' : '' }}
                                {{ $ticket->status === 'available' ? 'bg-gray-200 text-gray-800' : '' }}">

                                <input type="checkbox" wire:model.live="selectedTickets" value="{{ $ticket->id }}" class="absolute top-1 left-1 h-3 w-3 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">

                                <label for="ticket-{{ $ticket->id }}" class="block p-2 cursor-pointer">
                                    <span class="font-bold text-sm">{{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span class="block truncate text-xxs mt-1">{{ $ticket->user->name ?? '---' }}</span>
                                </label>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12 text-gray-500">
                                <p>Nenhuma cota encontrada com os filtros atuais.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
