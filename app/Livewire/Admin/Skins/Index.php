<?php

namespace App\Livewire\Admin\Skins;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads; // Para upload de imagens

class Index extends Component
{
    use WithFileUploads;

    public $products;
    public $isModalOpen = false;

    // Propriedades para o formulário
    public $name, $description, $wear, $price, $steam_inspect_link, $image;
    public $productId;

    protected $rules = [
        'name' => 'required|string|max:255',
        'wear' => 'required|string',
        'price' => 'required|numeric|min:0',
        'image' => 'required|image|max:1024', // 1MB Max
        'steam_inspect_link' => 'nullable|url'
    ];

    public function mount()
    {
        $this->products = Product::where('type', 'in_stock')->latest()->get();
    }

    public function render()
    {
        return view('livewire.admin.skins.index')->layout('layouts.admin'); // Assumindo que você tem um layout de admin
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        $product = Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'wear' => $this->wear,
            'price' => $this->price,
            'steam_inspect_link' => $this->steam_inspect_link,
            'type' => 'in_stock', // Definido como Pronta Entrega
            'status' => 'available', // Já nasce disponível
        ]);

        if ($this->image) {
            $product->addMedia($this->image->getRealPath())
                ->usingName($this->image->getClientOriginalName())
                ->toMediaCollection('product_images');
        }

        session()->flash('message', 'Skin adicionada com sucesso!');
        $this->isModalOpen = false;
        $this->mount(); // Recarrega a lista
    }

    // (Aqui você adicionaria os métodos edit(), update() e delete())

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        // ... resetar todas as propriedades
    }
}
