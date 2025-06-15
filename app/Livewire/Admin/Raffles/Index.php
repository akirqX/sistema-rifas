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

    public bool $showModal = false;
    public ?Raffle $editingRaffle = null;

    public string $title = '';
    public string $description = '';
    public ?float $ticket_price = null;
    public ?int $total_tickets = null;
    public $photo = null;

    // Propriedades para as estatísticas
    public float $totalRevenue = 0;
    public int $totalOrders = 0;
    public int $totalTicketsSold = 0;
    public int $activeRafflesCount = 0;
    public array $salesChartData = [];

    protected function rules(): array
    {
        $rules = [
            'title' => 'required|string|min:5',
            'description' => 'required|string',
            'ticket_price' => 'required|numeric|min:0.1',
            'photo' => 'nullable|image|max:2048',
        ];
        if (!$this->editingRaffle) {
            $rules['total_tickets'] = 'required|integer|min:10|max:10000';
        }
        return $rules;
    }

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
        $sales = Order::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(6)) // Inclui o dia de hoje
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $dates = collect(range(0, 6))->map(function ($day) use ($sales) {
            $date = now()->subDays($day)->format('Y-m-d');
            $sale = $sales->get($date);

            return [
                'label' => Carbon::parse($date)->format('d/m'),
                'total' => $sale ? $sale->total : 0,
            ];
        })->reverse()->values();

        $this->salesChartData = [
            'labels' => $dates->pluck('label')->all(),
            'data' => $dates->pluck('total')->all(),
        ];

        $this->dispatch('salesDataUpdated', $this->salesChartData);
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->reset();
        $this->editingRaffle = null;
        $this->showModal = true;
    }

    public function edit(Raffle $raffle): void
    {
        $this->resetValidation();
        $this->editingRaffle = $raffle;
        $this->title = $raffle->title;
        $this->description = $raffle->description;
        $this->ticket_price = $raffle->ticket_price;
        $this->total_tickets = $raffle->total_tickets;
        $this->photo = null;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $this->validate();
        try {
            DB::transaction(function () {
                $data = [
                    'title' => $this->title,
                    'description' => $this->description,
                    'ticket_price' => $this->ticket_price,
                ];
                if ($this->editingRaffle) {
                    $this->editingRaffle->update($data);
                    $raffleToProcess = $this->editingRaffle;
                    session()->flash('success', 'Rifa atualizada com sucesso!');
                } else {
                    $data['total_tickets'] = $this->total_tickets;
                    $data['status'] = 'pending';
                    $raffleToProcess = Raffle::create($data);

                    $tickets = [];
                    for ($i = 1; $i <= $this->total_tickets; $i++) {
                        $tickets[] = ['raffle_id' => $raffleToProcess->id, 'number' => $i, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()];
                    }
                    foreach (array_chunk($tickets, 1000) as $chunk) {
                        Ticket::insert($chunk);
                    }
                    session()->flash('success', 'Rifa criada com sucesso!');
                }
                if ($this->photo) {
                    $raffleToProcess->addMedia($this->photo->getRealPath())->toMediaCollection('raffles');
                }
            });
            $this->closeModal();
            $this->calculateStats();
            $this->prepareSalesChart();
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }

    public function activateRaffle(Raffle $raffle): void
    {
        if ($raffle->status === 'pending') {
            $raffle->update(['status' => 'active']);
            session()->flash('success', 'Rifa ativada com sucesso!');
        }
        $this->calculateStats();
        $this->prepareSalesChart();
    }

    public function cancelRaffle(Raffle $raffle): void
    {
        if ($raffle->tickets()->where('status', 'paid')->exists()) {
            session()->flash('error', 'Não é possível cancelar uma rifa que já possui cotas vendidas.');
            return;
        }
        $raffle->update(['status' => 'cancelled']);
        session()->flash('success', 'Rifa cancelada com sucesso.');
        $this->calculateStats();
        $this->prepareSalesChart();
    }

    public function performDraw(Raffle $raffle): void
    {
        $paidTickets = $raffle->tickets()->where('status', 'paid')->get();

        if ($paidTickets->isEmpty()) {
            session()->flash('error', 'Não é possível sortear uma rifa sem nenhuma cota paga.');
            return;
        }

        $winningTicket = $paidTickets->random();

        $raffle->update([
            'status' => 'finished',
            'winner_ticket_id' => $winningTicket->id,
            'drawn_at' => now(),
        ]);

        session()->flash('success', "Sorteio realizado! A cota vencedora é #{$winningTicket->number}.");
        $this->calculateStats();
        $this->prepareSalesChart();
    }

    public function render()
    {
        $raffles = Raffle::with('winner.user')->latest()->paginate(10);
        return view('livewire.admin.raffles.index', [
            'raffles' => $raffles,
        ])->layout('layouts.app');
    }
}
