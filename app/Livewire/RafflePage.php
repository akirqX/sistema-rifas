<?php
namespace App\Livewire;
use App\Models\Raffle;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
class RafflePage extends Component
{
    public Raffle $raffle;
    public $tickets;
    public $selectedTickets = [];
    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle->load('winner.user');
        $this->tickets = $this->raffle->tickets()->orderBy('number')->get();
    }
    public function selectTicket($ticketId)
    {
        $ticket = $this->tickets->find($ticketId);
        if ($ticket && $ticket->status !== 'available') {
            session()->flash('error', 'Esta cota não está mais disponível!');
            return;
        }
        if (isset($this->selectedTickets[$ticketId])) {
            unset($this->selectedTickets[$ticketId]);
        } else {
            $this->selectedTickets[$ticketId] = $ticketId;
        }
    }
    // ESTE MÉTODO AGORA É MAIS SIMPLES
    public function reserveTickets()
    {
        if (empty($this->selectedTickets))
            return;

        // Guarda as cotas na sessão e redireciona para o checkout
        Session::put('selected_tickets_for_' . $this->raffle->id, $this->selectedTickets);
        return $this->redirect(route('checkout', $this->raffle), navigate: true);
    }
    public function render()
    {
        return view('livewire.raffle-page')->layout('layouts.app');
    }
}
