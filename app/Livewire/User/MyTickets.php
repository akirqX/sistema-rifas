<?php

namespace App\Livewire\User;

use App\Models\Raffle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyTickets extends Component
{
    public function render()
    {
        // Busca todas as rifas DISTINTAS nas quais o usuário logado possui pelo menos uma cota PAGA.
        // O `distinct()` garante que cada rifa apareça apenas uma vez.
        // O `with('tickets')` carrega previamente as cotas para evitar múltiplas queries.
        $raffles = Raffle::whereHas('tickets', function ($query) {
            $query->where('user_id', Auth::id())->where('status', 'paid');
        })
            ->with([
                'tickets' => function ($query) {
                    // Garante que estamos carregando apenas as cotas do usuário logado.
                    $query->where('user_id', Auth::id())->where('status', 'paid')->orderBy('number');
                }
            ])
            ->get();

        return view('livewire.user.my-tickets', [
            'raffles' => $raffles,
        ])->layout('layouts.app');
    }
}
