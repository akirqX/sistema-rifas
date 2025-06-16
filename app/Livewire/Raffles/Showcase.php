<?php

namespace App\Livewire\Raffles;

use App\Models\Raffle;
use Livewire\Component;

class Showcase extends Component
{
    public function render()
    {
        $raffles = Raffle::where('status', 'active')
            ->with('media')
            ->latest()
            ->get();

        return view('livewire.raffles.showcase', [
            'raffles' => $raffles,
        ])->layout('layouts.app');
    }
}
