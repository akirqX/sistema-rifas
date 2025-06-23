<?php
namespace App\Livewire;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Arr;

class RafflePage extends Component
{
    public string $errorMessage = '';
    public Raffle $raffle;
    public array $selectedTickets = [];
    public array $occupiedNumbers = [];
    public int $totalTickets;
    public int $currentPage = 1;
    public int $perPage = 100;
    public int $totalPages;
    public float $progressPercent = 0;
    public bool $isLoading = false;
    public bool $showGuestModal = false;
    public string $guestName = '';
    public string $guestEmail = '';

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
        $this->totalTickets = $this->raffle->total_tickets;
        $this->totalPages = (int) ceil($this->totalTickets / $this->perPage);
    }

    public function render()
    {
        $tickets = $this->raffle->tickets()->orderBy('number')->get();
        $soldTickets = $tickets->whereIn('status', ['paid', 'reserved'])->count();
        $this->progressPercent = $this->totalTickets > 0 ? ($soldTickets / $this->totalTickets) * 100 : 0;
        $this->occupiedNumbers = $tickets->whereIn('status', ['paid', 'reserved'])->pluck('number')->toArray();
        return view('livewire.raffle-page')->layout('layouts.app');
    }

    public function selectTicket(string $ticketNumber): void
    {
        if (in_array($ticketNumber, $this->occupiedNumbers)) {
            return;
        }
        if (($key = array_search($ticketNumber, $this->selectedTickets)) !== false) {
            unset($this->selectedTickets[$key]);
        } else {
            $this->selectedTickets[] = $ticketNumber;
        }
        $this->selectedTickets = array_values($this->selectedTickets);
    }

    public function startCheckout()
    {
        $this->validate(['selectedTickets' => ['required', 'array', 'min:1']], ['selectedTickets.required' => 'Você precisa selecionar pelo menos uma cota.']);
        if (Auth::check()) {
            return $this->createOrderAndRedirect(Auth::id());
        }
        $this->showGuestModal = true;
    }

    public function processGuestCheckout()
    {
        $this->validate(['guestName' => 'required|string|max:255', 'guestEmail' => 'required|email|max:255', 'selectedTickets' => 'required|array|min:1']);
        return $this->createOrderAndRedirect(null, $this->guestName, $this->guestEmail);
    }

    protected function createOrderAndRedirect(?int $userId, ?string $guestName = null, ?string $guestEmail = null)
    {
        $this->isLoading = true;
        try {
            $order = DB::transaction(function () use ($userId, $guestName, $guestEmail) {
                $padding = strlen((string) $this->raffle->total_tickets);
                $formattedSelectedTickets = array_map(fn($n) => str_pad($n, $padding, '0', STR_PAD_LEFT), $this->selectedTickets);
                $ticketsToReserve = Ticket::where('raffle_id', $this->raffle->id)->whereIn('number', $formattedSelectedTickets)->lockForUpdate()->get();
                if ($ticketsToReserve->where('status', 'available')->count() !== count($this->selectedTickets)) {
                    throw new \Exception("Uma ou mais cotas selecionadas não estão mais disponíveis.");
                }
                $order = Order::create(['user_id' => $userId, 'guest_name' => $guestName, 'guest_email' => $guestEmail, 'raffle_id' => $this->raffle->id, 'ticket_quantity' => count($this->selectedTickets), 'total_amount' => count($this->selectedTickets) * $this->raffle->ticket_price, 'status' => 'pending', 'expires_at' => now('America/Sao_Paulo')->addMinutes(15)]);
                Ticket::whereIn('id', $ticketsToReserve->pluck('id'))->update(["status" => "reserved", "order_id" => $order->id, "user_id" => $userId]);
                return $order;
            });
            $this->reset('selectedTickets', 'showGuestModal', 'guestName', 'guestEmail');
            return $this->redirect(route('order.show', $order), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->isLoading = false;
            $this->showGuestModal = false;
        }
    }

    public function adjustSelection(int $count): void
    {
        if ($count > 0) {
            $padding = strlen((string) $this->raffle->total_tickets);
            $allPossibleNumbers = range(1, $this->totalTickets);
            $allPossibleNumbersFormatted = array_map(fn($n) => str_pad($n, $padding, '0', STR_PAD_LEFT), $allPossibleNumbers);
            $currentlyUnavailable = array_merge($this->occupiedNumbers, $this->selectedTickets);
            $availableForSelection = array_diff($allPossibleNumbersFormatted, $currentlyUnavailable);
            if (empty($availableForSelection))
                return;
            $keys = @array_rand($availableForSelection, min($count, count($availableForSelection)));
            $newSelections = is_array($keys) ? Arr::only($availableForSelection, $keys) : [$availableForSelection[$keys]];
            $this->selectedTickets = array_unique(array_merge($this->selectedTickets, $newSelections));
        } elseif ($count < 0) {
            $this->selectedTickets = array_slice($this->selectedTickets, 0, $count);
        }
    }
    public function clearSelection(): void
    {
        $this->selectedTickets = [];
    }
    public function changePage(int $page)
    {
        $this->currentPage = $page;
    }
}
