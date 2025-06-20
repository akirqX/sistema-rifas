<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;

class OrderShowPage extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // Garante que o usuário só possa ver seus próprios pedidos
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Carrega o pedido com as relações necessárias para a view
        $this->order = $order->load('raffle', 'tickets');
    }

    public function render()
    {
        // A cada atualização (pelo wire:poll), recarrega os dados do pedido do banco
        $this->order->refresh();

        return view('livewire.user.order-show-page')->layout('layouts.app');
    }
}
