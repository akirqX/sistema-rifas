<?php

namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination; // Importar o trait de paginação
use Illuminate\Support\Facades\DB;

class ManageTickets extends Component
{
    use WithPagination; // Usar o trait de paginação

    public Raffle $raffle;
    public int $totalTickets = 0, $paidCount = 0, $reservedCount = 0, $expiredCount = 0, $availableCount = 0;
    public bool $showTicketModal = false;
    public ?Ticket $selectedTicket = null;
    public string $filterStatus = '';
    public string $search = '';

    // Para o novo modal de criação manual
    public bool $showCreateModal = false;
    public $newTicketNumber;
    public $newTicketUserId;

    protected $paginationTheme = 'bootstrap'; // Usa um tema compatível com Tailwind

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
        $this->totalTickets = $raffle->total_tickets;
        $this->updateStats();
    }

    public function updateStats(): void
    {
        $stats = Ticket::where('raffle_id', $this->raffle->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->paidCount = $stats->get('paid', 0);
        $this->reservedCount = $stats->get('reserved', 0);
        $this->expiredCount = $stats->get('expired', 0);
        $this->availableCount = $this->totalTickets - ($this->paidCount + $this->reservedCount + $this->expiredCount);
    }

    public function openTicketModal(int $ticketId)
    {
        $this->selectedTicket = Ticket::with(['order.user'])->find($ticketId);
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
        $ticketNumber = $this->selectedTicket->number;
        DB::transaction(function () {
            $this->selectedTicket->order?->update(['status' => 'cancelled']);
            $this->selectedTicket->delete();
        });
        session()->flash('success', "Cota #{$ticketNumber} liberada com sucesso!");
        $this->updateStats();
        $this->showTicketModal = false;
        $this->selectedTicket = null;
    }

    public function approveTicket(): void
    {
        if (!$this->selectedTicket || !$this->selectedTicket->order) {
            session()->flash('error', 'Ação inválida.');
            $this->showTicketModal = false;
            return;
        }
        DB::transaction(function () {
            $this->selectedTicket->order->update(['status' => 'paid']);
            $this->selectedTicket->update(['status' => 'paid']);
        });
        session()->flash('success', "Cota #{$this->selectedTicket->number} aprovada!");
        $this->updateStats();
        $this->showTicketModal = false;
        $this->selectedTicket = null;
    }

    public function setStatusFilter(string $status): void
    {
        $this->resetPage(); // Reseta a paginação ao mudar de filtro
        $this->filterStatus = ($this->filterStatus === $status) ? '' : $status;
    }

    public function updatedSearch(): void
    {
        $this->resetPage(); // Reseta a paginação ao buscar
    }

    public function openCreateModal()
    {
        $this->reset(['newTicketNumber', 'newTicketUserId']);
        $this->showCreateModal = true;
    }

    public function createManualTicket()
    {
        $this->validate([
            'newTicketNumber' => 'required|integer|min:1|max:' . $this->totalTickets,
            'newTicketUserId' => 'required|exists:users,id',
        ]);

        $existingTicket = Ticket::where('raffle_id', $this->raffle->id)
            ->where('number', str_pad($this->newTicketNumber, strlen((string) $this->totalTickets), '0', STR_PAD_LEFT))
            ->first();

        if ($existingTicket) {
            $this->addError('newTicketNumber', 'Esta cota já está em uso.');
            return;
        }

        Ticket::create([
            'raffle_id' => $this->raffle->id,
            'user_id' => $this->newTicketUserId,
            'number' => str_pad($this->newTicketNumber, strlen((string) $this->totalTickets), '0', STR_PAD_LEFT),
            'status' => 'paid', // Cotas manuais são consideradas 'pagas'
            // Sem pedido (order_id = null)
        ]);

        session()->flash('success', 'Cota criada manualmente com sucesso!');
        $this->updateStats();
        $this->showCreateModal = false;
    }

    public function render()
    {
        $ticketsQuery = Ticket::query()
            ->with(['user', 'order.user'])
            ->where('raffle_id', $this->raffle->id);

        if (!empty($this->filterStatus)) {
            $ticketsQuery->where('status', $this->filterStatus);
        }

        if (!empty($this->search)) {
            $ticketsQuery->where(function ($query) {
                $query->where('number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('order', fn($q) => $q->where('guest_name', 'like', '%' . $this->search . '%'));
            });
        }

        // A lógica do filtro 'available' agora é implícita: não mostramos nada.
        if ($this->filterStatus === 'available') {
            $ticketsQuery->whereRaw('1 = 0'); // Força a query a não retornar nada
        }

        $tickets = $ticketsQuery->orderBy('number', 'asc')->paginate(20);

        return view('livewire.admin.raffles.manage-tickets', [
            'tickets' => $tickets, // Passa a coleção paginada para a view
        ])->layout('layouts.app');
    }
}
