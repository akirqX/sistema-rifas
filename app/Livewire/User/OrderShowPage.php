<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;

class OrderShowPage extends Component
{
    // A propriedade pública para armazenar o pedido.
    public Order $order;
    public ?array $pixData = null;

    // O método mount é o ÚNICO lugar onde a inicialização deve acontecer.
    public function mount(Order $order)
    {
        // Garante que o usuário logado só pode ver seus próprios pedidos.
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega as relações e prepara os dados.
        $this->order = $order->load('raffle.media', 'tickets');

        if (!empty($this->order->payment_details)) {
            $this->pixData = json_decode($this->order->payment_details, true);
        }
    }

    // O método render simplesmente renderiza a view.
    // O Livewire já torna a propriedade pública `$order` disponível para a view.
    public function render()
    {
        return view('livewire.user.order-show-page')->layout('layouts.app');
    }
}
