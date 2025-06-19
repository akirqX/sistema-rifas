<?php

namespace App\Livewire\Admin\Skins;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;

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
        'steam_inspect_link' => 'nullable|url'
    ];

    public function mount()
    {
        $this->products = Product::where('type', 'in_stock')->latest()->get();
    }

    public function render()
    {
        return view('livewire.admin.raffles.index');
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'image' => 'required|image|max:1024',
        ]));

        $product = Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'wear' => $this->wear,
            'price' => $this->price,
            'steam_inspect_link' => $this->steam_inspect_link,
            'type' => 'in_stock',
            'status' => 'available',
        ]);

        if ($this->image) {
            $product->addMedia($this->image->getRealPath())
                ->usingName($this->image->getClientOriginalName())
                ->toMediaCollection('product_images');
        }

        session()->flash('message', 'Skin adicionada com sucesso!');
        $this->isModalOpen = false;
        $this->mount();
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
