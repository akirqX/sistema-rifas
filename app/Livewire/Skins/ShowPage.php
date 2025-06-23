<?php

namespace App\Livewire\Skins;

use App\Models\Product;
use Livewire\Component;

class ShowPage extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        if ($product->type !== 'in_stock') {
            abort(404);
        }
        $this->product = $product;
    }

    public function startCheckout()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $checkoutData = [
            'type' => 'product',
            'item_id' => $this->product->id,
            'details' => [
                'quantity' => 1,
            ],
        ];

        session()->put('checkout_data', $checkoutData);

        // ==========================================================================
        // CORREÇÃO APLICADA AQUI
        // ==========================================================================
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.skins.show-page')->layout('layouts.app');
    }
}
