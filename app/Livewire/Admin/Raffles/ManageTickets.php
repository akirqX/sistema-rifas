<?php

namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTickets extends Component
{
    use WithPagination;

    public Raffle $raffle;
    public $search = '';
    public $statusFilter = '';

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
    }

    public function cancelTicket(Ticket $ticket)
    {
        if ($ticket->status === 'paid' || $ticket->status === 'pending') {
            $ticket->update(['status' => 'available', 'user_id' => null, 'order_id' => null]);
            session()->flash('success', 'Cota #' . $ticket->number . ' liberada com sucesso.');
        }
    }

    public function render()
    {
        $ticketsQuery = $this->raffle->tickets()->with('user', 'order');

        if ($this->search) {
            $ticketsQuery->where(function ($query) {
                $query->where('number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $ticketsQuery->where('status', $this->statusFilter);
        }

        $tickets = $ticketsQuery->orderBy('number', 'asc')->paginate(50);

        return view('livewire.admin.raffles.manage-tickets', [
            'tickets' => $tickets,
        ])->layout('layouts.app');
    }
}
