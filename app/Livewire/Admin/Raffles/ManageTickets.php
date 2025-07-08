<?php
namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageTickets extends Component
{
    public Raffle $raffle;
    public array $ticketMap = [];
    public int $totalTickets = 0, $paidCount = 0, $reservedCount = 0, $expiredCount = 0, $availableCount = 0;
    public bool $showTicketModal = false;
    public ?Ticket $selectedTicket = null;

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
        $this->totalTickets = $raffle->total_tickets;
        $this->loadTicketData();
    }
    public function loadTicketData(): void
    {
        $tickets = $this->raffle->tickets()->with(['user', 'order'])->get();
        $this->ticketMap = $tickets->keyBy(fn($ticket) => intval($ticket->number))->all();
        $stats = $tickets->countBy('status');
        $this->paidCount = $stats->get('paid', 0);
        $this->reservedCount = $stats->get('reserved', 0);
        $this->availableCount = $stats->get('available', 0);
        $this->expiredCount = $stats->get('expired', 0);
    }
    public function openTicketModal(int $ticketId)
    {
        $this->selectedTicket = Ticket::with(['user', 'order'])->find($ticketId);
        $this->showTicketModal = true;
    }

    public function forceCancelTicket(): void
    {
        if (!$this->selectedTicket)
            return;
        if ($this->selectedTicket->status === 'paid') {
            session()->flash('error', 'Não é possível liberar uma cota que já foi paga.');
        } else {
            DB::transaction(function () {
                $ticketNumber = $this->selectedTicket->number;
                // Se o ticket estiver associado a um pedido, cancela o pedido.
                $this->selectedTicket->order?->update(['status' => 'cancelled']);
                // Libera o ticket.
                $this->selectedTicket->update(['status' => 'available', 'order_id' => null, 'user_id' => null]);
                session()->flash('success', "Cota #{$ticketNumber} liberada com sucesso!");
            });
        }
        $this->loadTicketData();
        $this->showTicketModal = false;
    }

    public function approveTicket(): void
    {
        if (!$this->selectedTicket)
            return;
        DB::transaction(function () {
            $this->selectedTicket->order?->update(['status' => 'paid']);
            $this->selectedTicket->update(['status' => 'paid']);
        });
        session()->flash('success', "Cota #{$this->selectedTicket->number} aprovada manualmente!");
        $this->loadTicketData();
        $this->showTicketModal = false;
    }

    public function render()
    {
        return view('livewire.admin.raffles.manage-tickets')->layout('layouts.app');
    }
}
