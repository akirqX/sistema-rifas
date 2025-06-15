<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Minhas Cotas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">Suas cotas em cada rifa</h3>

                    <div class="space-y-4">
                        @forelse ($raffles as $raffle)
                            <div x-data="{ open: false }" class="border rounded-lg">
                                <!-- Cabeçalho do Acordeão -->
                                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left bg-gray-50 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <img class="h-12 w-16 object-cover rounded-md" src="{{ $raffle->getFirstMediaUrl('raffles', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Rifa' }}" alt="Imagem da rifa">
                                        <div class="ml-4">
                                            <div class="font-bold">{{ $raffle->title }}</div>
                                            <div class="text-sm text-gray-600">Você possui {{ $raffle->tickets->count() }} cotas nesta rifa.</div>
                                        </div>
                                    </div>
                                    <!-- Ícone de seta que gira -->
                                    <svg class="w-6 h-6 transform transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <!-- Conteúdo do Acordeão (cotas) -->
                                <div x-show="open" x-collapse class="p-4 border-t">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($raffle->tickets as $ticket)
                                            <span class="bg-blue-500 text-white font-mono font-bold text-sm py-1 px-3 rounded-full">
                                                {{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-500">
                                <p>Você ainda não possui nenhuma cota paga.</p>
                                <a href="{{ route('raffles.showcase') }}" class="mt-4 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded-lg">Ver Rifas Ativas</a>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
