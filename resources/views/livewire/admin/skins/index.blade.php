<div>
    <div class="container mx-auto px-4 py-8 sm:py-12">

        {{-- Mensagem de Sucesso --}}
        @if (session()->has('message'))
            <div class="bg-green-500/20 text-green-300 p-4 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Gerenciar Skins (Arsenal)</h1>
            <button wire:click="create()" class="btn-prodgio btn-primary">Adicionar Nova Skin</button>
        </div>

        {{-- Tabela de Skins --}}
        <div class="bg-panel-dark border border-border-subtle rounded-lg overflow-hidden">
            <table class="w-full text-left text-text-muted">
                <thead class="bg-gray-900/50 text-sm uppercase text-text-muted">
                    <tr>
                        <th class="p-4">Nome da Skin</th>
                        <th class="p-4">Preço</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-t border-border-subtle">
                            <td class="p-4 font-medium text-white">{{ $product->name }}</td>
                            <td class="p-4">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->status == 'available' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                {{-- TODO: Adicionar a função de editar --}}
                                <a href="#" class="text-indigo-400 hover:text-indigo-300">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-lg">Nenhuma skin de pronta entrega cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de Criação/Edição --}}
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-panel-dark border border-border-subtle p-8 rounded-lg w-full max-w-2xl">
                <h2 class="text-xl font-bold text-white mb-4">Adicionar Nova Skin</h2>
                <form wire:submit.prevent="store" class="space-y-4">

                    <div>
                        <label for="name" class="block text-sm font-medium text-text-muted mb-1">Nome da Skin</label>
                        <input type="text" id="name" wire:model.defer="name" class="input-prodgio w-full">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="wear" class="block text-sm font-medium text-text-muted mb-1">Exterior (Wear)</label>
                            <input type="text" id="wear" wire:model.defer="wear" class="input-prodgio w-full" placeholder="Ex: Field-Tested">
                            @error('wear') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-text-muted mb-1">Preço (R$)</label>
                            <input type="number" step="0.01" id="price" wire:model.defer="price" class="input-prodgio w-full">
                            @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-text-muted mb-1">Descrição</label>
                        <textarea id="description" wire:model.defer="description" rows="3" class="input-prodgio w-full"></textarea>
                    </div>

                    <div>
                        <label for="steam_inspect_link" class="block text-sm font-medium text-text-muted mb-1">Link de Inspeção (Opcional)</label>
                        <input type="url" id="steam_inspect_link" wire:model.defer="steam_inspect_link" class="input-prodgio w-full">
                        @error('steam_inspect_link') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-text-muted mb-1">Imagem da Skin</label>
                        <input type="file" id="image" wire:model="image" class="input-prodgio w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                        @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                        {{-- Loading State --}}
                        <div wire:loading wire:target="image" class="text-sm text-gray-400 mt-2">Carregando imagem...</div>

                        {{-- Preview da Imagem --}}
                        @if ($image)
                            <div class="mt-4">
                                <p class="text-sm text-text-muted mb-2">Pré-visualização:</p>
                                <img src="{{ $image->temporaryUrl() }}" class="rounded-lg max-h-40">
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="btn-prodgio btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-prodgio btn-primary">Salvar Skin</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

{{-- Adicionando uma classe CSS que pode estar faltando para o input de arquivo --}}
@push('styles')
<style>
    .input-prodgio {
        /* Adapte ao seu estilo, mas isso garante um visual base */
        background-color: #1f2937;
        border: 1px solid #374151;
        color: white;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }
</style>
@endpush
