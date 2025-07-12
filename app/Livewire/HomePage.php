<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Raffle;
use Livewire\Component;

class HomePage extends Component
{
    public $featuredRaffles;
    public $latestWinners;
    public $featuredSkins;
    public ?Raffle $nextRaffleToEnd = null; // Propriedade para o contador

    public function mount()
    {
        $this->featuredRaffles = Raffle::where('status', 'active')
            ->withCount('ticketsSold')
            ->latest()
            ->take(8)
            ->get();

        $this->latestWinners = Raffle::where('status', 'finished')
            ->whereNotNull('winner_ticket_id')
            ->with('winnerTicket.user')
            ->latest('drawn_at')
            ->take(3)
            ->get();

        $this->featuredSkins = Product::where('type', 'in_stock')
            ->where('status', 'available')
            ->latest()
            ->take(3)
            ->get();

        // LÓGICA DO CONTADOR: Busca a rifa ativa que tem a data de sorteio mais próxima
        $this->nextRaffleToEnd = Raffle::where('status', 'active')
            ->whereNotNull('drawn_at') // Apenas rifas com data definida
            ->orderBy('drawn_at', 'asc') // Ordena pela data mais próxima
            ->first();
    }

    public function render()
    {
        return view('livewire.home-page')->layout('layouts.app');
    }
}
