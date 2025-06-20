<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Dashboard extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    // Propriedades de Skins (Ainda aqui, podem ser movidas no futuro)
    public bool $showSkinModal = false;
    public ?Product $editingProduct = null;
    public string $skin_name = '';
    public string $skin_description = '';
    public string $skin_wear = '';
    public ?float $skin_price = null;
    public ?string $steam_inspect_link = '';
    public $skin_image = null;

    // Propriedades de Estatísticas e Busca
    public float $totalRevenue = 0;
    public int $totalOrders = 0;
    public int $totalTicketsSold = 0;
    public int $activeRafflesCount = 0;
    public array $salesChartData = [];
    public $searchSkins = '';

    public function mount()
    {
        $this->calculateStats();
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
        // IMPORTANTE: COPIE A SUA LÓGICA ORIGINAL DO GRÁFICO AQUI
    }

    protected function rulesForSkin(): array
    {
        // IMPORTANTE: COPIE A SUA LÓGICA DE VALIDAÇÃO DE SKIN AQUI
    }

    public function saveSkin()
    {
        // IMPORTANTE: COPIE A SUA LÓGICA DE SALVAR SKIN AQUI
    }

    private function resetSkinForm()
    {
        // IMPORTANTE: COPIE A SUA LÓGICA DE RESETAR FORM DE SKIN AQUI
    }

    public function openSkinModal()
    {
        $this->resetValidation();
        $this->resetSkinForm();
        $this->showSkinModal = true;
    }

    public function editSkin(Product $product)
    {
        $this->resetValidation();
        $this->editingProduct = $product;
        $this->skin_name = $product->name;
        $this->skin_description = $product->description;
        $this->skin_wear = $product->wear;
        $this->skin_price = $product->price;
        $this->steam_inspect_link = $product->steam_inspect_link;
        $this->skin_image = null;
        $this->showSkinModal = true;
    }

    public function render()
    {
        // O Dashboard agora só busca por SKINS. As rifas são problema do outro componente.
        $products = Product::where('name', 'like', '%' . $this->searchSkins . '%')
            ->where('type', 'in_stock')
            ->latest()
            ->paginate(5, ['*'], 'productsPage');

        // Note a mudança aqui! O layout agora é 'layouts.admin'
        return view('livewire.admin.dashboard', [
            'products' => $products,
        ])->layout('layouts.admin');
    }
}
