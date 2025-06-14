<div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($raffles as $raffle)
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            {{-- Imagem da Rifa (vamos usar um placeholder por enquanto) --}}
                            <div class="bg-gray-200 h-48 flex items-center justify-center">
                                <span class="text-gray-500">Imagem da Rifa</span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold mb-2">{{ $raffle->title }}</h3>
                                <p class="text-gray-700 text-sm mb-4">{{ Str::limit($raffle->description, 100) }}</p>

                                <div class="mb-4">
                                    @php
                                        $sold = $raffle->tickets()->where('status', 'paid')->count();
                                        $total = $raffle->total_tickets;
                                        $percentage = $total > 0 ? ($sold / $total) * 100 : 0;
                                    @endphp
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 mt-1">{{ $sold }} de {{ $total }} cotas vendidas</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-green-500">R$ {{ number_format($raffle->ticket_price, 2, ',', '.') }}</span>
                                    <a href="{{ route('raffle.show', $raffle) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
