<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Meus Pedidos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">Seu Histórico de Compras</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">Data</th>
                                    <th class="py-2 px-4 border-b">Rifa</th>
                                    <th class="py-2 px-4 border-b">Qtd. Cotas</th>
                                    <th class="py-2 px-4 border-b">Valor Total</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr class="text-center hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-2 px-4 border-b text-left">{{ $order->raffle->title }}</td>
                                        <td class="py-2 px-4 border-b">{{ $order->ticket_quantity }}</td>
                                        <td class="py-2 px-4 border-b">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $order->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $order->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">Você ainda não fez nenhum pedido.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
