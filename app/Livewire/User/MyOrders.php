<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination; // IMPORTANTE: Adicionar esta linha

class MyOrders extends Component
{
    use WithPagination; // IMPORTANTE: E esta linha também

    public function render()
    {
        // CORREÇÃO: Trocamos ->get() por ->paginate(10)
        // Isso vai buscar os pedidos do usuário, 10 por página,
        // e retornar um objeto de paginação que a view pode usar.
        $orders = Order::where('user_id', auth()->id())
            ->with('raffle') // Otimização para carregar os dados da rifa
            ->latest() // Ordena do mais recente para o mais antigo
            ->paginate(10);

        return view('livewire.user.my-orders', [
            'orders' => $orders,
        ])->layout('layouts.app');
    }
}
