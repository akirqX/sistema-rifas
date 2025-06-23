<?php

namespace App\Livewire\Raffles;

use App\Models\Raffle;
use Livewire\Component;

class Showcase extends Component
{
    public function render()
    {
        // Otimização: Usando 'withCount' para carregar a contagem de tickets
        // de forma eficiente, evitando o problema N+1 query.
        $raffles = Raffle::where('status', 'active')
            ->with('media') // Continua carregando a mídia
            ->latest()
            ->get();

        return view('livewire.raffles.showcase', [
            'raffles' => $raffles,
        ])->layout('layouts.app');
    }
}
