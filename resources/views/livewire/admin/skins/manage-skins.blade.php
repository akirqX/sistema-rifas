<div class="space-y-8">
    {{-- GERENCIAMENTO DE SKINS --}}
    <div class="bg-panel-dark border border-border-subtle rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                <h3 class="text-2xl font-bold text-white mb-3 sm:mb-0">Gerenciamento de Skins</h3>
                <button wire:click="openSkinModal" class="btn-prodgio btn-secondary w-full sm:w-auto justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Nova Skin</span>
                </button>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchSkins" placeholder="Buscar skins pelo nome..." class="form-input w-full mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-bg-tertiary">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-semibold text-text-muted uppercase tracking-wider">Skin</th>
                            <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 text-center text-xs font-semibold text-text-muted uppercase tracking-wider">Preço</th>
                            <th class="py-3 px-6 text-right text-xs font-semibold text-text-muted uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse ($products as $product)
                            <tr wire:key="product-{{ $product->id }}" class="hover:bg-gray-800/50">
                                <td class="py-4 px-6 whitespace-nowrap"><div class="flex items-center"><div class="flex-shrink-0 h-12 w-16"><img class="h-12 w-16 object-contain rounded-md" src="{{ $product->getFirstMediaUrl('product_images', 'thumb') ?: 'https://via.placeholder.com/150x150.png?text=Skin' }}" alt=""></div><div class="ml-4"><div class="text-sm font-medium text-white">{{ $product->name }}</div><div class="text-xs text-text-subtle">{{ $product->wear }}</div></div></div></td>
                                <td class="py-4 px-6 whitespace-nowrap text-center"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->status === 'available' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">{{ ucfirst($product->status) }}</span></td>
                                <td class="py-4 px-6 whitespace-nowrap text-center text-sm text-white">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="editSkin({{ $product->id }})" class="text-indigo-400 hover:text-indigo-300">Editar</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-text-muted">Nenhuma skin encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $products->links(data: ['scrollTo' => false]) }}</div>
        </div>
    </div>

    {{-- MODAL PARA CRIAR/EDITAR SKIN --}}
    @if ($showSkinModal)
        <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4" x-data x-trap.noscroll.inert="showSkinModal">
            <div class="bg-panel-dark p-8 rounded-2xl w-full max-w-2xl border border-border-subtle max-h-screen overflow-y-auto">
                <h2 class="text-2xl font-bold text-white mb-6">{{ $editingProduct ? 'Editar Skin' : 'Adicionar Nova Skin' }}</h2>
                <form wire:submit.prevent="saveSkin" class="space-y-4">
                    <div><label for="skin_name" class="form-label">Nome da Skin</label><input type="text" id="skin_name" wire:model.defer="skin_name" class="form-input">@error('skin_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label for="skin_wear" class="form-label">Exterior (Wear)</label><input type="text" id="skin_wear" wire:model.defer="skin_wear" class="form-input" placeholder="Ex: Field-Tested">@error('skin_wear') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                        <div><label for="skin_price" class="form-label">Preço (R$)</label><input type="number" step="0.01" id="skin_price" wire:model.defer="skin_price" class="form-input">@error('skin_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    </div>
                    <div><label for="skin_description" class="form-label">Descrição</label><textarea id="skin_description" wire:model.defer="skin_description" rows="3" class="form-textarea"></textarea>@error('skin_description')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                    <div><label for="steam_inspect_link" class="form-label">Link de Inspeção (Opcional)</label><input type="url" id="steam_inspect_link" wire:model.defer="steam_inspect_link" class="form-input">@error('steam_inspect_link') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    <div><label for="skin_image" class="form-label">Imagem da Skin</label><input type="file" id="skin_image" wire:model="skin_image" class="form-input-file">@if ($skin_image) <img src="{{ $skin_image->temporaryUrl() }}" class="mt-4 h-32 w-auto rounded">@elseif($editingProduct && $editingProduct->getFirstMedia('product_images')) <img src="{{ $editingProduct->getFirstMediaUrl('product_images') }}" class="mt-4 h-32 w-auto rounded" alt="Imagem atual">@endif @error('skin_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror</div>
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="$wire.set('showSkinModal', false)" class="btn-prodgio btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-prodgio btn-primary" wire:loading.attr="disabled"><span wire:loading.remove>Salvar Skin</span><span wire:loading>Salvando...</span></button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
