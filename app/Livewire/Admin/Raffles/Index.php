<?php

namespace App\Livewire\Admin\Raffles;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    // Todas as suas propriedades originais, restauradas
    public bool $showModal = false;
    public ?Raffle $editingRaffle = null;
    public string $title = '';
    public string $description = '';
    public ?float $price = null;
    public ?int $total_numbers = null;
    public $photo = null;

    public bool $showDrawModal = false;
    public ?Raffle $raffleToDraw = null;
    public ?int $winner_ticket_number = null;

    // Propriedades de estatísticas, restauradas
    public float $totalRevenue = 0;
    public int $totalOrders = 0;
    public int $totalTicketsSold = 0;
    public int $activeRafflesCount = 0;
    public array $salesChartData = [];

    // Propriedade para a busca
    public $search = '';

    protected function rules(): array
    {
        $rules = [
            'title' => 'required|string|min:5',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.1',
            'photo' => 'nullable|image|max:2048',
        ];
        if (!$this->editingRaffle) {
            $rules['total_numbers'] = 'required|integer|min:10|max:10000';
        }
        return $rules;
    }

    // Todos os seus métodos originais estão corretos
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
    { /* ... seu código ... */
    }
    public function create(): void
    { /* ... seu código ... */
    }
    public function edit(Raffle $raffle): void
    { /* ... seu código ... */
    }
    public function closeModal(): void
    { /* ... seu código ... */
    }
    public function save(): void
    { /* ... seu código ... */
    }
    public function showDrawModal(Raffle $raffle)
    { /* ... seu código ... */
    }
    public function closeDrawModal()
    { /* ... seu código ... */
    }
    public function setWinner()
    { /* ... seu código ... */
    }
    public function performRandomDraw(Raffle $raffle)
    { /* ... seu código ... */
    }
    public function activateRaffle(Raffle $raffle): void
    { /* ... seu código ... */
    }
    public function cancelRaffle(Raffle $raffle): void
    { /* ... seu código ... */
    }

    // --- A SOLUÇÃO ESTÁ AQUI ---
    public function render()
    {
        // As propriedades públicas ($totalRevenue, etc.) já estão disponíveis para a view.
        // Nós só precisamos buscar e passar os dados que não são propriedades, como a lista paginada de rifas.
        return view('livewire.admin.raffles.index', [
            'raffles' => Raffle::where('title', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ]);
    }
}
