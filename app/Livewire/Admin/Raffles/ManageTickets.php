<?php

namespace App\Livewire\Admin\Raffles;

use App\Models\Raffle;
use App\Models\Ticket;
use Livewire\Component;

class ManageTickets extends Component
{
    public Raffle $raffle;
    public array $ticketMap = [];

    // Estatísticas Corretas
    public int $totalTickets = 0;
    public int $paidCount = 0;
    public int $pendingCount = 0;
    public int $expiredCount = 0;
    public int $orphanCount = 0;

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
        $this->totalTickets = $raffle->total_numbers;
        $this->loadTicketData();
    }

    /**
     * Carrega e categoriza TODOS os tickets da rifa.
     */
    public function loadTicketData(): void
    {
        // Pega todos os tickets, sem filtros, e mapeia pelo número.
        $tickets = $this->raffle->tickets()->with('user')->get();
        $this->ticketMap = $tickets->keyBy('number')->all();

        // Conta cada status de forma precisa.
        $stats = $tickets->countBy('status');
        $this->pendingCount = $stats->get('pending', 0);
        $this->expiredCount = $stats->get('expired', 0);

        // Contagens especiais para válidas e órfãs
        $this->paidCount = $tickets->where('status', 'paid')->whereNotNull('user_id')->count();
        $this->orphanCount = $tickets->where('status', 'paid')->whereNull('user_id')->count();
    }

    /**
     * Libera (deleta) qualquer cota que não seja uma compra paga e válida.
     */
    public function forceCancelTicket(int $ticketId): void
    {
        $ticket = Ticket::find($ticketId);

        // A única condição para NÃO deletar é ser uma cota paga E válida.
        if ($ticket && ($ticket->status === 'paid' && $ticket->user_id !== null)) {
            session()->flash('error', 'Não é possível cancelar uma cota paga e válida.');
            return;
        }

        if ($ticket) {
            $ticketNumber = $ticket->number;
            $ticket->delete();
            session()->flash('success', "Cota #{$ticketNumber} liberada com sucesso!");
            $this->loadTicketData(); // Recarrega os dados para a tela
        }
    }

    public function render()
    {
        return view('livewire.admin.raffles.manage-tickets')
            ->layout('components.admin-layout');
    }
}
