<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyOrders extends Component
{
    public function render()
    {
        // Busca todos os pedidos do usuário logado, junto com a informação da rifa relacionada,
        // ordenados pelos mais recentes.
        $orders = Auth::user()->orders()->with('raffle')->latest()->get();

        return view('livewire.user.my-orders', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}
