<?php

namespace App\Livewire\User;

use App\Models\Raffle;
use Livewire\Component;

class MyTickets extends Component
{
    public function render()
    {
        // 1. Busca no banco todas as rifas onde o usuário logado tem tickets pagos.
        //    O `with()` otimiza o carregamento dos tickets e das imagens da rifa.
        $raffles = Raffle::whereHas('tickets', function ($query) {
            $query->where('user_id', auth()->id())->where('status', 'paid');
        })->with([
                    'tickets' => function ($query) {
                        $query->where('user_id', auth()->id())->where('status', 'paid');
                    },
                    'media'
                ])->get();

        // 2. CORREÇÃO: Pega TODOS os tickets de todas as rifas e coloca em uma única coleção.
        //    Esta é a variável `$tickets` que estava faltando na view.
        $tickets = $raffles->flatMap(function ($raffle) {
            return $raffle->tickets;
        });

        // 3. Envia AMBAS as coleções (`$raffles` e a nova `$tickets`) para a view.
        return view('livewire.user.my-tickets', [
            'raffles' => $raffles, // A view de 'Meus Pedidos' usava isso, pode ser útil
            'tickets' => $tickets, // A nova view de 'Minhas Cotas' precisa desta
        ])->layout('layouts.app');
    }
}
