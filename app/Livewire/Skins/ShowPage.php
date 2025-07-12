<?php

namespace App\Livewire\Skins;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ShowPage extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        if ($product->type !== 'in_stock' || $product->quantity <= 0) {
            abort(404);
        }
        $this->product = $product;
    }

    public function startCheckout()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ($this->product->quantity <= 0) {
            $this->addError('stock', 'Este item não está mais disponível em estoque.');
            return;
        }

        $order = DB::transaction(function () {
            $order = Order::create([
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
                'raffle_id' => null,
                'total_amount' => $this->product->price,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(15),
            ]);

            $this->product->decrement('quantity');

            return $order;
        });

        return redirect()->route('order.show', $order);
    }

    public function render()
    {
        // CORRIGIDO: Deve apontar para a view de skins, não de orders.
        return view('livewire.skins.show-page')->layout('layouts.app');
    }
}
