<?php

namespace App\Livewire\Skins;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class IndexPage extends Component
{
    use WithPagination;

    public string $sortBy = 'latest';
    public array $filterType = [];

    // Supondo que você tenha estas colunas no seu DB. Se não, remova ou adapte.
    // Ex: 'weapon_type' -> ['Faca', 'Rifle', 'Pistola']
    // Ex: 'rarity' -> ['Covert', 'Classified', 'Restricted']
    public array $types = ['Faca', 'Rifle', 'Pistola', 'SMG'];
    public array $rarities = ['Covert', 'Classified', 'Restricted', 'Mil-Spec'];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $skinsQuery = Product::query()
            ->where('type', 'in_stock')
            ->where('status', 'available');

        // Aplicar filtros
        if (!empty($this->filterType)) {
            $skinsQuery->whereIn('weapon_type', $this->filterType);
        }

        // Aplicar ordenação
        if ($this->sortBy === 'price_asc') {
            $skinsQuery->orderBy('price', 'asc');
        } elseif ($this->sortBy === 'price_desc') {
            $skinsQuery->orderBy('price', 'desc');
        } else {
            $skinsQuery->latest();
        }

        return view('livewire.skins.index-page', [
            'skins' => $skinsQuery->paginate(12)
        ])->layout('layouts.app');
    }

    public function updating($key)
    {
        if (in_array($key, ['sortBy', 'filterType'])) {
            $this->resetPage();
        }
    }
}
