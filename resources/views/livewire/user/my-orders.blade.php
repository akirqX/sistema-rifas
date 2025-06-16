<div>
    <section class="section">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h1 class="section-title">Meus Pedidos</h1>
                <p class="section-subtitle">Acompanhe o status de todas as suas compras de cotas.</p>
            </div>
            @if(session('success'))
                <div class="max-w-4xl mx-auto mb-4 rounded-md bg-green-500/20 p-4 text-sm text-green-300 border border-green-500/30">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-bg-secondary border border-border rounded-lg shadow-lg overflow-hidden max-w-4xl mx-auto">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-bg-tertiary">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-semibold text-text-muted uppercase tracking-wider">Rifa</th>
                                <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Data</th>
                                <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Valor</th>
                                <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Status</th>
                                <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-bg-tertiary transition-colors duration-200">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="font-medium text-text-light">{{ $order->raffle->title }}</span>
                                        <span class="block text-xs text-text-subtle">ID: {{ $order->id }}</span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-center text-sm text-text-muted">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-center text-sm font-semibold text-primary-light">
                                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-center">
                                        <a href="{{ route('my.orders.show', $order) }}" class="cta-secondary text-xs py-2 px-3">
                                            Ver Detalhes
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-text-muted">
                                        Você ainda não fez nenhum pedido.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($orders->hasPages())
                    <div class="p-4 bg-bg-secondary border-t border-border">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
