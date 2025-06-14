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

    // Propriedades para o formulário
    public bool $showModal = false;
    public ?Raffle $editingRaffle = null;

    public string $title = '';
    public string $description = '';
    public ?float $ticket_price = null;
    public ?int $total_tickets = null;

    protected function rules(): array
    {
        // Regras base que se aplicam sempre
        $rules = [
            'title' => 'required|string|min:5',
            'description' => 'required|string',
            'ticket_price' => 'required|numeric|min:0.1',
        ];

        // Adiciona a regra para 'total_tickets' APENAS se estivermos criando uma nova rifa
        if (!$this->editingRaffle) {
            $rules['total_tickets'] = 'required|integer|min:10|max:10000';
        }

        return $rules;
    }

    // Configura e abre a modal para CRIAR uma nova rifa
    public function create(): void
    {
        $this->resetValidation();
        $this->reset();
        $this->editingRaffle = null;
        $this->showModal = true;
    }

    // Configura e abre a modal para EDITAR uma rifa existente
    public function edit(Raffle $raffle): void
    {
        $this->resetValidation();
        $this->editingRaffle = $raffle;

        $this->title = $raffle->title;
        $this->description = $raffle->description;
        $this->ticket_price = $raffle->ticket_price;
        $this->total_tickets = $raffle->total_tickets;

        $this->showModal = true;
    }

    // Fecha a modal
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    // Salva a rifa (cria uma nova ou atualiza uma existente)
    public function save(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                if ($this->editingRaffle) {
                    $this->editingRaffle->update([
                        'title' => $this->title,
                        'description' => $this->description,
                        'ticket_price' => $this->ticket_price,
                    ]);
                    session()->flash('success', 'Rifa atualizada com sucesso!');
                } else {
                    $raffle = Raffle::create([
                        'title' => $this->title,
                        'description' => $this->description,
                        'ticket_price' => $this->ticket_price,
                        'total_tickets' => $this->total_tickets,
                        'status' => 'pending',
                    ]);

                    $tickets = [];
                    for ($i = 1; $i <= $this->total_tickets; $i++) {
                        $tickets[] = ['raffle_id' => $raffle->id, 'number' => $i, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()];
                    }
                    foreach (array_chunk($tickets, 1000) as $chunk) {
                        Ticket::insert($chunk);
                    }
                    session()->flash('success', 'Rifa criada com sucesso!');
                }
            });

            $this->closeModal();

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
    }

    public function cancelRaffle(Raffle $raffle): void
    {
        if ($raffle->tickets()->where('status', 'paid')->exists()) {
            session()->flash('error', 'Não é possível cancelar uma rifa que já possui cotas vendidas.');
            return;
        }

        $raffle->update(['status' => 'cancelled']);
        session()->flash('success', 'Rifa cancelada com sucesso.');
    }

    public function render()
    {
        $raffles = Raffle::latest()->paginate(10);
        return view('livewire.admin.raffles.index', ['raffles' => $raffles])->layout('layouts.app');
    }
}
