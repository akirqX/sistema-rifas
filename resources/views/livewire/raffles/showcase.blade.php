<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($raffles as $raffle)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        {{-- Imagem da Rifa --}}
                        <a href="{{ route('raffle.show', $raffle) }}">
    <div class="aspect-w-16 aspect-h-9">
        <img class="w-full h-full object-center object-cover"
             src="{{ $raffle->getFirstMediaUrl('raffles') ?: 'https://via.placeholder.com/1600x900.png?text=Sem+Imagem' }}"
             alt="Imagem da rifa {{ $raffle->title }}">
    </div>
</a>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2 truncate">{{ $raffle->title }}</h3>
                            <p class="text-gray-700 text-sm mb-4 h-10">{{ Str::limit($raffle->description, 100) }}</p>

                            <div class="mb-4">
                                @php
                                    $sold = $raffle->tickets()->where('status', 'paid')->count();
                                    $total = $raffle->total_tickets;
                                    $percentage = $total > 0 ? ($sold / $total) * 100 : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 mt-1">{{ $sold }} de {{ $total }} cotas vendidas</span>
                            </div>

                            <div class="flex justify-between items-center mt-6">
                                <span class="text-2xl font-bold text-green-500">R$ {{ number_format($raffle->ticket_price, 2, ',', '.') }}</span>
                                <a href="{{ route('raffle.show', $raffle) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                    Participar
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">Nenhuma rifa ativa no momento. Volte em breve!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
