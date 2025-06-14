<?php

namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Propriedades para o formulário de criação
    public bool $showCreateModal = false;
    public string $title = '';
    public string $description = '';
    public float $ticket_price = 0;
    public int $total_tickets = 0;

    // Regras de validação
    protected function rules()
    {
        return [
            'title' => 'required|string|min:5',
            'description' => 'required|string',
            'ticket_price' => 'required|numeric|min:0.1',
            'total_tickets' => 'required|integer|min:10|max:10000', // Limite máximo para performance
        ];
    }

    // Adicione este método dentro da classe Index
    public function activateRaffle(Raffle $raffle): void
    {
        if ($raffle->status === 'pending') {
            $raffle->update(['status' => 'active']);
            session()->flash('success', 'Rifa ativada com sucesso!');
        }
    }

    // Abre a modal e reseta os campos
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset('title', 'description', 'ticket_price', 'total_tickets');
        $this->showCreateModal = true;
    }

    // Fecha a modal
    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    // Método para salvar a nova rifa
    public function save(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // 1. Cria a Rifa
                $raffle = Raffle::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'ticket_price' => $this->ticket_price,
                    'total_tickets' => $this->total_tickets,
                    'status' => 'pending', // Começa como pendente
                ]);

                // 2. Cria as cotas (tickets) para essa rifa
                $tickets = [];
                for ($i = 1; $i <= $this->total_tickets; $i++) {
                    $tickets[] = [
                        'raffle_id' => $raffle->id,
                        'number' => $i,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Insere em lotes para melhor performance
                foreach (array_chunk($tickets, 1000) as $chunk) {
                    Ticket::insert($chunk);
                }
            });

            session()->flash('success', 'Rifa criada com sucesso!');
            $this->closeCreateModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro ao criar a rifa: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $raffles = Raffle::latest()->paginate(10);

        return view('livewire.admin.raffles.index', [
            'raffles' => $raffles,
        ])->layout('layouts.app');
    }
}
