<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Livewire\Component;

class DashboardStats extends Component
{
    public float $totalRevenue = 0;
    public int $totalOrders = 0;
    public int $totalTicketsSold = 0;
    public int $activeRafflesCount = 0;
    public array $salesChartData = [];

    public function mount()
    {
        $this->calculateStats();
        // Recupere aqui seus dados para o gráfico de vendas
        $this->prepareSalesChart();
    }

    public function calculateStats()
    {
        $this->totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $this->totalOrders = Order::count();
        $this->totalTicketsSold = Ticket::where('status', 'paid')->count();
        $this->activeRafflesCount = Raffle::where('status', 'active')->count();
    }

    public function prepareSalesChart()
    {
        // Coloque aqui sua lógica real para pegar dados do gráfico
        $this->salesChartData = [
            'labels' => ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'],
            'data' => [150, 230, 224, 218, 135, 147, 260],
        ];

        // Dispara um evento para o Javascript do gráfico
        $this->dispatch('salesDataUpdated', $this->salesChartData);
    }


    public function render()
    {
        return view('livewire.admin.dashboard-stats');
    }
}
