<?php

namespace App\Livewire\Skins;

use App\Models\Product;
use Livewire\Component;

class IndexPage extends Component
{
    public function render()
    {
        $skins = Product::where('type', 'in_stock')
                        ->where('status', 'available')
                        ->latest()
                        ->get();

        return view('livewire.skins.index-page', [
            'skins' => $skins
        ])->layout('layouts.app');
    }
}
