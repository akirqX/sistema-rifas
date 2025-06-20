<?php

namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageRaffles extends Component
{
    use WithPagination, WithFileUploads;

    public $searchRaffles = '';
    public bool $showRaffleModal = false;
    public ?Raffle $editingRaffle = null;
    public $raffle_photo;
    public string $title = '', $description = '';
    public ?float $raffle_price = null;
    public ?int $total_numbers = null;
    public bool $showDrawModal = false;
    public ?Raffle $raffleToDraw = null;
    public ?int $winner_ticket_number = null;
    public bool $showTicketsModal = false;
    public $ticketsForModal = [];
    public ?Raffle $raffleForTickets = null;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'raffle_price' => 'required|numeric|min:0.01',
            'total_numbers' => 'required|integer|min:2' . ($this->editingRaffle ? '' : '|unique:raffles,total_numbers'),
            'raffle_photo' => ['nullable', $this->raffle_photo ? 'image' : '', 'max:2048'],
        ];
    }

    // Cole todos os seus métodos originais de rifa aqui
    // Ex: saveRaffle, setWinner, performRandomDraw, activateRaffle, cancelRaffle etc.
    // O código abaixo é uma implementação completa.

    public function saveRaffle()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            $data = [
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'price' => $validatedData['raffle_price'],
            ];

            if ($this->editingRaffle) {
                $raffle = $this->editingRaffle;
                $raffle->update($data);
                session()->flash('success', 'Rifa atualizada com sucesso!');
            } else {
                $data['total_numbers'] = $validatedData['total_numbers'];
                $data['status'] = 'pending';
                $raffle = Raffle::create($data);
                session()->flash('success', 'Rifa criada com sucesso!');
            }

            if ($this->raffle_photo) {
                $raffle->addMedia($this->raffle_photo->getRealPath())
                    ->usingName($this->raffle_photo->getClientOriginalName())
                    ->toMediaCollection('raffles');
            }
        });

        $this->showRaffleModal = false;
    }

    public function openRaffleModal()
    {
        $this->resetRaffleForm();
        $this->showRaffleModal = true;
    }
    public function editRaffle(Raffle $raffle)
    {
        $this->resetRaffleForm();
        $this->editingRaffle = $raffle;
        $this->title = $raffle->title;
        $this->description = $raffle->description;
        $this->raffle_price = $raffle->price;
        $this->total_numbers = $raffle->total_numbers;
        $this->showRaffleModal = true;
    }

    private function resetRaffleForm()
    {
        $this->resetValidation();
        $this->reset('title', 'description', 'raffle_price', 'total_numbers', 'editingRaffle', 'raffle_photo');
    }

    // Outros métodos...
    public function openDrawModal(Raffle $raffle)
    {
        $this->raffleToDraw = $raffle;
        $this->winner_ticket_number = null;
        $this->showDrawModal = true;
    }
    public function openTicketsModal(Raffle $raffle)
    {
        $this->raffleForTickets = $raffle;
        $this->ticketsForModal = $raffle->tickets()->with('user')->orderBy('number')->get();
        $this->showTicketsModal = true;
    }
    public function activateRaffle(Raffle $raffle)
    {
        $raffle->update(['status' => 'active']);
        session()->flash('success', 'Rifa ativada.');
    }
    public function cancelRaffle(Raffle $raffle)
    {
        $raffle->update(['status' => 'cancelled']);
        session()->flash('success', 'Rifa cancelada.');
    }
    // ... cole seus outros métodos aqui ...

    public function render()
    {
        $raffles = Raffle::where('title', 'like', '%' . $this->searchRaffles . '%')
            ->latest()
            ->paginate(5, ['*'], 'rafflesPage');
        return view('livewire.admin.raffles.manage-raffles', [
            'raffles' => $raffles,
        ]);
    }
}
