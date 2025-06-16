<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;

class OrderShowPage extends Component
{
    // A propriedade pública para armazenar o pedido.
    // O Livewire vai injetar o objeto Order aqui automaticamente a partir da rota.
    public Order $order;

    // O método mount não é estritamente necessário para inicializar a propriedade
    // quando usamos o route-model binding do Livewire, mas é bom para verificações
    // de segurança e para carregar relações.
    public function mount(Order $order)
    {
        // Garante que o usuário logado só pode ver seus próprios pedidos.
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega as relações para otimizar a view.
        $this->order = $order->load('raffle.media', 'tickets');
    }

    // O método render simplesmente renderiza a view.
    // O Livewire já torna a propriedade pública `$order` disponível para a view.
    public function render()
    {
        return view('livewire.user.order-show-page')->layout('layouts.app');
    }
}
