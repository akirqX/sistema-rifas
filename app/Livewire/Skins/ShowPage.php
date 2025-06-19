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

    public function render()
    {
        return view('livewire.skins.show-page')->layout('layouts.app');
    }
}
