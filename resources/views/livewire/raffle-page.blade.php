<div>
    {{-- A view da nossa página de rifa será inserida dentro do layout do Breeze --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session()->has('error'))
                <div class="mb-4 p-4 bg-red-200 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-2">{{ $raffle->title }}</h1>
                    <p class="text-gray-600 mb-8">{{ $raffle->description }}</p>

                    <div class="grid grid-cols-5 sm:grid-cols-10 md:grid-cols-15 lg:grid-cols-20 gap-1 text-xs">
                        @foreach ($tickets as $ticket)
                            @php
                                $isSelected = isset($selectedTickets[$ticket->id]);
                                $isReserved = $ticket->status === 'reserved';
                                $isPaid = $ticket->status === 'paid';

                                $class = 'bg-gray-200 hover:bg-blue-400 text-gray-800'; // Available
                                if ($isSelected)
                                    $class = 'bg-green-500 text-white ring-2 ring-offset-2 ring-green-500';
                                if ($isReserved)
                                    $class = 'bg-yellow-500 text-white cursor-not-allowed';
                                if ($isPaid)
                                    $class = 'bg-red-600 text-white cursor-not-allowed';
                            @endphp
                            <button @if($ticket->status === 'available') wire:click="selectTicket({{ $ticket->id }})" @else
                            disabled @endif
                                class="p-2 rounded-md text-center font-mono font-semibold transition-all duration-150 {{ $class }}">
                                {{ str_pad($ticket->number, 4, '0', STR_PAD_LEFT) }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARRA INFERIOR DE COMPRA --}}
    <div class="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 p-4 shadow-lg z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-white sm:px-6 lg:px-8">
            <div>
                <span class="text-sm text-gray-400">Selecionadas</span>
                <p class="text-2xl font-bold">{{ count($selectedTickets) }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Total</span>
                <p class="text-2xl font-bold text-green-400">R$
                    {{ number_format(count($selectedTickets) * $raffle->ticket_price, 2, ',', '.') }}</p>
            </div>
            <button wire:click="reserveTickets" wire:loading.attr="disabled"
                class="bg-green-500 hover:bg-green-600 font-bold py-3 px-8 rounded-lg text-lg disabled:opacity-50 disabled:cursor-wait">
                <span wire:loading.remove>COMPRAR</span>
                <span wire:loading>AGUARDE...</span>
            </button>
        </div>
    </div>
</div>