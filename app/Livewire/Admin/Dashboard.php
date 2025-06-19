<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithPagination, WithFileUploads;

    // Propriedades de Rifas
    public bool $showRaffleModal = false;
    public ?Raffle $editingRaffle = null;
    public string $title = '';
    public string $description = '';
    public ?float $raffle_price = null;
    public ?int $total_numbers = null;
    public $raffle_photo = null;

    // Propriedades de Sorteio
    public bool $showDrawModal = false;
    public ?Raffle $raffleToDraw = null;
    public ?int $winner_ticket_number = null;

    // --- A SOLUÇÃO ESTÁ AQUI ---
    // Propriedades de Cotas, que estavam faltando
    public bool $showTicketsModal = false;
    public $ticketsForModal = [];
    public ?Raffle $raffleForTickets = null;

    // Propriedades de Skins
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
    public $searchRaffles = '';
    public $searchSkins = '';

    // MÉTODOS ORIGINAIS (Preservados)
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
    { /* ... seu código original ... */
    }
    protected function rulesForRaffle(): array
    { /* ... seu código original ... */
    }
    protected function rulesForSkin(): array
    { /* ... seu código original ... */
    }
    public function saveRaffle()
    { /* ... seu código original de save ... */
    }
    public function setWinner()
    { /* ... seu código original de setWinner ... */
    }
    public function performRandomDraw(Raffle $raffle)
    { /* ... seu código original ... */
    }
    public function activateRaffle(Raffle $raffle): void
    { /* ... seu código original ... */
    }
    public function cancelRaffle(Raffle $raffle): void
    { /* ... seu código original ... */
    }
    public function saveSkin()
    { /* ... sua lógica de saveSkin ... */
    }
    private function resetRaffleForm()
    { /* ... */
    }
    private function resetSkinForm()
    { /* ... */
    }

    // MÉTODOS PARA ABRIR MODAIS (Com nomes corrigidos)
    public function openRaffleModal()
    {
        $this->resetValidation();
        $this->resetRaffleForm();
        $this->showRaffleModal = true;
    }
    public function openSkinModal()
    {
        $this->resetValidation();
        $this->resetSkinForm();
        $this->showSkinModal = true;
    }
    public function openDrawModal(Raffle $raffle)
    {
        $this->raffleToDraw = $raffle;
        $this->winner_ticket_number = null;
        $this->showDrawModal = true;
    }
    public function openTicketsModal(Raffle $raffle)
    {
        $this->raffleForTickets = $raffle;
        $this->ticketsForModal = $raffle->tickets()->with('user')->get();
        $this->showTicketsModal = true;
    }
    public function editRaffle(Raffle $raffle)
    {
        $this->resetValidation();
        $this->editingRaffle = $raffle;
        $this->title = $raffle->title;
        $this->description = $raffle->description;
        $this->raffle_price = $raffle->price;
        $this->total_numbers = $raffle->total_numbers;
        $this->raffle_photo = null;
        $this->showRaffleModal = true;
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

    // MÉTODO RENDER FINAL E UNIFICADO
    public function render()
    {
        $raffles = Raffle::where('title', 'like', '%' . $this->searchRaffles . '%')->latest()->paginate(5, ['*'], 'rafflesPage');
        $products = Product::where('name', 'like', '%' . $this->searchSkins . '%')->where('type', 'in_stock')->latest()->paginate(5, ['*'], 'productsPage');

        return view('livewire.admin.dashboard', [
            'raffles' => $raffles,
            'products' => $products,
        ])->layout('layouts.app');
    }
}
