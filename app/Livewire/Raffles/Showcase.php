<?php

namespace App\Livewire\Raffles;

use App\Models\Raffle;
use Livewire\Component;

class Showcase extends Component
{
    public function render()
    {
        // Busca apenas as rifas ativas, ordenadas pelas mais recentes
        $raffles = Raffle::where('status', 'active')->latest()->get();

        return view('livewire.raffles.showcase', [
            'raffles' => $raffles,
        ])->layout('layouts.app');
    }
}
