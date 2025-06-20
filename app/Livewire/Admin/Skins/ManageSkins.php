<?php

namespace App\Livewire\Admin\Skins;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageSkins extends Component
{
    use WithPagination, WithFileUploads;

    public $searchSkins = '';
    public bool $showSkinModal = false;
    public ?Product $editingProduct = null;
    public $skin_image;
    public string $skin_name = '', $skin_description = '', $skin_wear = '';
    public ?float $skin_price = null;
    public ?string $steam_inspect_link = '';

    protected function rules()
    {
        return [
            'skin_name' => 'required|string|max:255',
            'skin_description' => 'nullable|string',
            'skin_wear' => 'required|string|max:100',
            'skin_price' => 'required|numeric|min:0.01',
            'steam_inspect_link' => 'nullable|url',
            'skin_image' => ['nullable', $this->skin_image ? 'image' : '', 'max:2048'],
        ];
    }

    // Cole aqui seus métodos originais de skin (saveSkin, etc)
    // Abaixo uma implementação completa:
    public function saveSkin()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            $data = [
                'name' => $validatedData['skin_name'],
                'description' => $validatedData['skin_description'],
                'wear' => $validatedData['skin_wear'],
                'price' => $validatedData['skin_price'],
                'steam_inspect_link' => $validatedData['steam_inspect_link'],
            ];

            if ($this->editingProduct) {
                $product = $this->editingProduct;
                $product->update($data);
                session()->flash('success', 'Skin atualizada com sucesso!');
            } else {
                $data['type'] = 'in_stock';
                $product = Product::create($data);
                session()->flash('success', 'Skin criada com sucesso!');
            }

            if ($this->skin_image) {
                $product->addMedia($this->skin_image->getRealPath())
                    ->usingName($this->skin_image->getClientOriginalName())
                    ->toMediaCollection('product_images');
            }
        });

        $this->showSkinModal = false;
    }

    public function openSkinModal()
    {
        $this->resetSkinForm();
        $this->showSkinModal = true;
    }
    public function editSkin(Product $product)
    {
        $this->resetSkinForm();
        $this->editingProduct = $product;
        $this->skin_name = $product->name;
        $this->skin_description = $product->description;
        $this->skin_wear = $product->wear;
        $this->skin_price = $product->price;
        $this->steam_inspect_link = $product->steam_inspect_link;
        $this->showSkinModal = true;
    }
    private function resetSkinForm()
    {
        $this->resetValidation();
        $this->reset('editingProduct', 'skin_name', 'skin_description', 'skin_wear', 'skin_price', 'steam_inspect_link', 'skin_image');
    }

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->searchSkins . '%')
            ->where('type', 'in_stock')
            ->latest()
            ->paginate(5, ['*'], 'productsPage');
        return view('livewire.admin.skins.manage-skins', [
            'products' => $products,
        ]);
    }
}
