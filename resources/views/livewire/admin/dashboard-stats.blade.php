<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle shadow-lg"><h4 class="text-sm font-medium text-text-muted">Total Arrecadado</h4><p class="text-3xl font-bold text-primary-light mt-2">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p></div>
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle shadow-lg"><h4 class="text-sm font-medium text-text-muted">Pedidos Totais</h4><p class="text-3xl font-bold text-white mt-2">{{ $totalOrders }}</p></div>
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle shadow-lg"><h4 class="text-sm font-medium text-text-muted">Cotas Vendidas</h4><p class="text-3xl font-bold text-white mt-2">{{ $totalTicketsSold }}</p></div>
        <div class="bg-panel-dark p-6 rounded-2xl border border-border-subtle shadow-lg"><h4 class="text-sm font-medium text-text-muted">Rifas Ativas</h4><p class="text-3xl font-bold text-white mt-2">{{ $activeRafflesCount }}</p></div>
    </div>
    <div class="mt-8 bg-panel-dark p-6 rounded-2xl border border-border-subtle shadow-lg" wire:ignore>
        <h4 class="font-bold text-white mb-4">Vendas nos últimos 7 dias</h4>
        <div class="h-64"><canvas id="salesChart"></canvas></div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('salesChart')?.getContext('2d');
            if (!ctx) return;
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Vendas',
                        data: [],
                        backgroundColor: 'rgba(52, 211, 153, 0.1)',
                        borderColor: '#34D399',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            @this.on('salesDataUpdated', salesData => {
                if(salesChart) {
                    salesChart.data.labels = salesData[0].labels;
                    salesChart.data.datasets[0].data = salesData[0].data;
                    salesChart.update();
                }
            });
        });
    </script>
    @endpush
</div>
