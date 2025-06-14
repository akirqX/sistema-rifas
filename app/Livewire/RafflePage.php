<?php
namespace App\Livewire;
use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
class RafflePage extends Component
{
    public Raffle $raffle;
    public $tickets;
    public $selectedTickets = [];
    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
        $this->tickets = $this->raffle->tickets()->orderBy('number')->get();
    }
    public function selectTicket($ticketId)
    {
        $ticket = $this->tickets->find($ticketId);
        if ($ticket && $ticket->status !== 'available') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Esta cota não está mais disponível!']);
            return;
        }
        if (isset($this->selectedTickets[$ticketId])) {
            unset($this->selectedTickets[$ticketId]);
        } else {
            $this->selectedTickets[$ticketId] = $ticketId;
        }
    }
    public function reserveTickets()
    {
        if (empty($this->selectedTickets))
            return;
        $order = null;
        try {
            DB::transaction(function () use (&$order) {
                $ticketsToReserve = Ticket::whereIn('id', $this->selectedTickets)->where('status', 'available')->lockForUpdate()->get();
                if (count($ticketsToReserve) !== count($this->selectedTickets)) {
                    throw new \Exception('Ops! Algumas cotas foram reservadas enquanto você escolhia. A página será atualizada.');
                }
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'raffle_id' => $this->raffle->id,
                    'ticket_quantity' => count($ticketsToReserve),
                    'total_amount' => count($ticketsToReserve) * $this->raffle->ticket_price,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(10),
                ]);
                Ticket::whereIn('id', $ticketsToReserve->pluck('id'))->update([
                    'status' => 'reserved',
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ]);
            });
            return redirect()->route('payment.page', ['order' => $order->id]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect(request()->header('Referer'));
        }
    }
    public function render()
    {
        return view('livewire.raffle-page')->layout('layouts.app');
    }
}