<?php

namespace App\Livewire;

use App\Models\Raffle;
use Livewire\Component;

class HomePage extends Component
{
    public $featuredRaffles;
    public $latestWinners;

    public function mount()
    {
        // Lógica para buscar as rifas em destaque (ex: as 4 mais recentes com status 'active')
        $this->featuredRaffles = Raffle::where('status', 'active')->latest()->take(4)->get();

        // --- CORRIGIDO AQUI ---
        // Trocamos 'winner_id' por 'winner_ticket_id' para corresponder ao seu banco de dados.
        // Também usamos 'with' para carregar as relações de forma otimizada e evitar mais erros.
        $this->latestWinners = Raffle::where('status', 'finished')
            ->whereNotNull('winner_ticket_id') // AQUI ESTÁ A CORREÇÃO PRINCIPAL
            ->with('winnerTicket.user') // Otimização importante
            ->latest()
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.home-page')->layout('layouts.app');
    }
}
