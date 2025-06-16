<?php

namespace App\Livewire;

use App\Models\Raffle;
use Livewire\Component;

class RafflePage extends Component
{
    public Raffle $raffle;
    public array $selectedTickets = [];
    public int $quantity = 1;
    public $tickets;

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle->load('tickets');
        $this->tickets = $this->raffle->tickets;
    }

    public function selectTicket($ticketNumber)
    {
        if (in_array($ticketNumber, $this->selectedTickets)) {
            $this->selectedTickets = array_diff($this->selectedTickets, [$ticketNumber]);
        } else {
            $this->selectedTickets[] = $ticketNumber;
        }
    }

    public function selectRandomTickets()
    {
        $this->validate(['quantity' => 'required|integer|min:1']);

        $availableTickets = $this->raffle->tickets()
            ->where('status', 'available')
            ->inRandomOrder()
            ->limit($this->quantity)
            ->pluck('number')
            ->toArray();

        $this->selectedTickets = array_unique(array_merge($this->selectedTickets, $availableTickets));

        if (count($availableTickets) < $this->quantity) {
            session()->flash('info', 'Foram encontradas apenas ' . count($availableTickets) . ' cotas disponíveis.');
        }
    }

    public function reserveTickets()
    {
        if (empty($this->selectedTickets)) {
            session()->flash('error', 'Você precisa selecionar pelo menos uma cota para continuar.');
            return;
        }

        session([
            'checkout_raffle_id' => $this->raffle->id,
            'checkout_tickets' => $this->selectedTickets,
        ]);

        return $this->redirect(route('checkout', ['raffle' => $this->raffle->id]), navigate: true);
    }

    public function render()
    {
        return view('livewire.raffle-page', [
            'tickets' => $this->tickets
        ])->layout('layouts.app');
    }
}
