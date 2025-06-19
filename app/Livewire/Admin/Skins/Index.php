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
    public $productId; // Para edição futura

    // Removi a validação da imagem daqui para ser condicional
    protected $rules = [
        'name' => 'required|string|max:255',
        'wear' => 'required|string',
        'price' => 'required|numeric|min:0',
        'steam_inspect_link' => 'nullable|url'
    ];

    public function mount()
    {
        $this->products = Product::where('type', 'in_stock')->latest()->get();
    }

    public function render()
    {
        // <-- A ÚNICA CORREÇÃO NECESSÁRIA!
        // Apontando para o layout correto que existe no seu projeto.
        return view('livewire.admin.skins.index')
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function store()
    {
        // Adicionando a validação da imagem apenas na criação
        $this->validate(array_merge($this->rules, [
            'image' => 'required|image|max:1024', // 1MB Max
        ]));

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

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->wear = '';
        $this->price = '';
        $this->steam_inspect_link = '';
        $this->image = null;
        $this->productId = null;
    }
}
