<?php
namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB; // Importar DB
use Livewire\Component;

class ManageTickets extends Component
{
    public Raffle $raffle;
    public array $ticketMap = [];
    public int $totalTickets = 0;
    public int $paidCount = 0;
    public int $reservedCount = 0;
    public int $expiredCount = 0;
    public int $availableCount = 0;
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
            $this->showTicketModal = false;
            return;
        }

        DB::transaction(function () {
            $ticketNumber = $this->selectedTicket->number;
            $order = $this->selectedTicket->order;

            // Libera o ticket
            $this->selectedTicket->update(['status' => 'available', 'order_id' => null, 'user_id' => null]);

            // Se o ticket pertencia a um pedido, marca o pedido como cancelado
            if ($order) {
                $order->update(['status' => 'cancelled']);
            }
            session()->flash('success', "Cota #{$ticketNumber} liberada e pedido associado cancelado.");
        });

        $this->loadTicketData();
        $this->showTicketModal = false;
    }

    public function approveTicket(): void
    {
        if (!$this->selectedTicket)
            return;
        DB::transaction(function () {
            // Garante que o pedido associado seja marcado como pago
            $this->selectedTicket->order?->update(['status' => 'paid']);
            // E o ticket também
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
