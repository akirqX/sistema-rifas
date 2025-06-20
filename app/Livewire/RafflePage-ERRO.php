<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Raffle;
use App\Models\Ticket;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RafflePage extends Component
{
    public Raffle $raffle;
    public array $selectedTickets = [];
    public array $occupiedNumbers = [];

    // Propriedades para a view
    public int $totalTickets;
    public int $currentPage = 1;
    public int $perPage = 100;
    public int $totalPages;
    public float $progressPercent = 0;

    public function mount(Raffle $raffle)
    {
        $this->raffle = $raffle;
        $this->totalTickets = $this->raffle->total_numbers;
        $this->totalPages = (int) ceil($this->totalTickets / $this->perPage);
    }

    public function render()
    {
        // A cada atualização, ele pega o status mais recente de TODOS os tickets
        $this->tickets = $this->raffle->tickets()->orderBy('number')->get();
        return view('livewire.raffle-page')->layout('layouts.app');
    }

    public function selectTicket(string $ticketNumber): void
    {
        if (in_array($ticketNumber, $this->occupiedNumbers))
            return;
        if (($key = array_search($ticketNumber, $this->selectedTickets)) !== false) {
            unset($this->selectedTickets[$key]);
        } else {
            $this->selectedTickets[] = $ticketNumber;
        }
        $this->selectedTickets = array_values($this->selectedTickets);
    }

    // O método de reserva, corrigido para funcionar.
    public function reserveTickets()
    {
        if (empty($this->selectedTickets)) {
            session()->flash('error', 'Você precisa selecionar pelo menos uma cota para continuar.');
            return;
        }

        try {
            $order = DB::transaction(function () {
                $ticketsToReserve = Ticket::where('raffle_id', $this->raffle->id)
                    ->whereIn('number', $this->selectedTickets)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->get();

                if (count($ticketsToReserve) !== count($this->selectedTickets)) {
                    throw new \Exception('Ops! Algumas cotas não estão mais disponíveis.');
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'raffle_id' => $this->raffle->id,
                    'ticket_quantity' => count($this->selectedTickets),
                    'total_amount' => count($this->selectedTickets) * $this->raffle->price,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(10),
                ]);

                Ticket::whereIn('id', $ticketsToReserve->pluck('id'))->update([
                    'status' => 'reserved',
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ]);

                return $order;
            });

            $this->selectedTickets = [];
            return redirect()->route('my.orders.show', ['order' => $order->id]);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return;
        }
    }

    // Funções de Ações Rápidas
    public function adjustSelection(int $count): void
    {
        if ($count > 0) {
            $allPossibleNumbers = range(1, $this->totalTickets);
            $allPossibleNumbersFormatted = array_map(fn($n) => str_pad($n, 4, '0', STR_PAD_LEFT), $allPossibleNumbers);
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
